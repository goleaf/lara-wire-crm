<?php

namespace App\Console\Commands;

use App\Support\Git\CommitMessageBuilder;
use Illuminate\Console\Attributes\AsCommand;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Contracts\Process\ProcessResult;
use Illuminate\Support\Facades\Process;

#[Signature('git:auto-commit {--dry-run : Generate and print the commit message without creating a commit} {--no-stage : Skip git add -A and only use already staged changes}')]
#[AsCommand(name: 'git:auto-commit')]
#[Description('Stage changes and create an automatic commit message with full changed-file details')]
class GitAutoCommitCommand extends Command
{
    public function __construct(private readonly CommitMessageBuilder $messageBuilder)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->insideGitRepository()) {
            $this->error('This command must be run inside a Git repository.');

            return self::FAILURE;
        }

        if (! $this->option('no-stage')) {
            $stageResult = $this->runGit('git add -A');

            if (! $stageResult->successful()) {
                $this->error('Unable to stage changes with git add -A.');
                $this->warn(trim($stageResult->errorOutput()));

                return self::FAILURE;
            }
        }

        $nameStatusResult = $this->runGit('git diff --cached --name-status');

        if (! $nameStatusResult->successful()) {
            $this->error('Unable to read staged file statuses.');
            $this->warn(trim($nameStatusResult->errorOutput()));

            return self::FAILURE;
        }

        $changes = $this->parseNameStatusOutput($nameStatusResult->output());

        if ($changes === []) {
            $this->error('No staged changes were found.');

            return self::FAILURE;
        }

        $numStatResult = $this->runGit('git diff --cached --numstat');

        if (! $numStatResult->successful()) {
            $this->error('Unable to read staged line-delta information.');
            $this->warn(trim($numStatResult->errorOutput()));

            return self::FAILURE;
        }

        $changesWithDeltas = $this->mergeLineDeltas(
            $changes,
            $this->parseNumStatOutput($numStatResult->output()),
        );

        $message = $this->messageBuilder->build($changesWithDeltas);

        if ($this->option('dry-run')) {
            $this->line('DRY RUN');
            $this->newLine();
            $this->line($message);

            return self::SUCCESS;
        }

        $temporaryMessageFile = tempnam(sys_get_temp_dir(), 'auto-commit-message-');

        if ($temporaryMessageFile === false) {
            $this->error('Unable to create a temporary commit message file.');

            return self::FAILURE;
        }

        file_put_contents($temporaryMessageFile, $message.PHP_EOL);

        try {
            $commitResult = $this->runGit(sprintf('git commit --file=%s', escapeshellarg($temporaryMessageFile)));
        } finally {
            @unlink($temporaryMessageFile);
        }

        if (! $commitResult->successful()) {
            $this->error('Git commit failed.');
            $this->warn(trim($commitResult->errorOutput()));

            return self::FAILURE;
        }

        $this->info('Auto-generated commit created.');
        $this->line(trim($commitResult->output()));

        return self::SUCCESS;
    }

    private function insideGitRepository(): bool
    {
        $result = $this->runGit('git rev-parse --is-inside-work-tree');

        return $result->successful() && trim($result->output()) === 'true';
    }

    private function runGit(string $command): ProcessResult
    {
        return Process::path(base_path())->run($command);
    }

    /**
     * @return array<int, array{status: string, path: string, additions: int|string|null, deletions: int|string|null}>
     */
    private function parseNameStatusOutput(string $output): array
    {
        $changes = [];

        foreach (preg_split('/\R/', trim($output)) as $line) {
            if ($line === '') {
                continue;
            }

            $parts = explode("\t", $line);
            $status = $parts[0] ?? '';

            if ($status === '') {
                continue;
            }

            $normalizedStatus = substr($status, 0, 1);

            if (in_array($normalizedStatus, ['R', 'C'], true) && isset($parts[1], $parts[2])) {
                $path = sprintf('%s -> %s', $parts[1], $parts[2]);
            } else {
                $path = $parts[1] ?? '';
            }

            if ($path === '') {
                continue;
            }

            $changes[] = [
                'status' => $normalizedStatus,
                'path' => $path,
                'additions' => null,
                'deletions' => null,
            ];
        }

        return $changes;
    }

    /**
     * @return array<int, array{additions: int|string, deletions: int|string}>
     */
    private function parseNumStatOutput(string $output): array
    {
        $deltas = [];

        foreach (preg_split('/\R/', trim($output)) as $line) {
            if ($line === '') {
                continue;
            }

            $parts = explode("\t", $line);

            if (count($parts) < 3) {
                continue;
            }

            $deltas[] = [
                'additions' => $this->normalizeDelta($parts[0]),
                'deletions' => $this->normalizeDelta($parts[1]),
            ];
        }

        return $deltas;
    }

    /**
     * @param  array<int, array{status: string, path: string, additions: int|string|null, deletions: int|string|null}>  $changes
     * @param  array<int, array{additions: int|string, deletions: int|string}>  $deltas
     * @return array<int, array{status: string, path: string, additions: int|string|null, deletions: int|string|null}>
     */
    private function mergeLineDeltas(array $changes, array $deltas): array
    {
        foreach ($changes as $index => $change) {
            if (! isset($deltas[$index])) {
                continue;
            }

            $changes[$index]['additions'] = $deltas[$index]['additions'];
            $changes[$index]['deletions'] = $deltas[$index]['deletions'];
        }

        return $changes;
    }

    private function normalizeDelta(string $value): int|string
    {
        if ($value !== '' && ctype_digit($value)) {
            return (int) $value;
        }

        return $value;
    }
}
