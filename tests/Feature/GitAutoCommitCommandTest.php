<?php

use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Process;

test('git auto commit stages, summarizes, and commits', function () {
    Process::fake([
        'git rev-parse --is-inside-work-tree' => Process::result(output: "true\n"),
        'git add -A' => Process::result(),
        'git diff --cached --name-status' => Process::result(output: implode("\n", [
            'M	app/Console/Commands/CrmInstallCommand.php',
            'A	app/Support/Git/CommitMessageBuilder.php',
            'D	tests/Feature/OldTest.php',
            '',
        ])),
        'git diff --cached --numstat' => Process::result(output: implode("\n", [
            '10	2	app/Console/Commands/CrmInstallCommand.php',
            '42	0	app/Support/Git/CommitMessageBuilder.php',
            '0	7	tests/Feature/OldTest.php',
            '',
        ])),
        'git commit --file=*' => Process::result(output: '[main abc1234] chore(auto): update 3 files (+52/-9)'),
    ]);

    $this->artisan('git:auto-commit')
        ->expectsOutputToContain('Auto-generated commit created.')
        ->assertSuccessful();

    Process::assertRan('git add -A');
    Process::assertRan('git diff --cached --name-status');
    Process::assertRan('git diff --cached --numstat');
    Process::assertRan(fn (PendingProcess $process) => str_starts_with($process->command, 'git commit --file='));
});

test('git auto commit supports dry run without creating commit', function () {
    Process::fake([
        'git rev-parse --is-inside-work-tree' => Process::result(output: "true\n"),
        'git add -A' => Process::result(),
        'git diff --cached --name-status' => Process::result(output: "M	app/Models/User.php\n"),
        'git diff --cached --numstat' => Process::result(output: "3	1	app/Models/User.php\n"),
    ]);

    $this->artisan('git:auto-commit', ['--dry-run' => true])
        ->expectsOutputToContain('DRY RUN')
        ->assertSuccessful();

    Process::assertDidntRun(fn (PendingProcess $process) => str_starts_with($process->command, 'git commit --file='));
});

test('git auto commit fails when there are no staged changes', function () {
    Process::fake([
        'git rev-parse --is-inside-work-tree' => Process::result(output: "true\n"),
        'git add -A' => Process::result(),
        'git diff --cached --name-status' => Process::result(output: ''),
    ]);

    $this->artisan('git:auto-commit')
        ->expectsOutputToContain('No staged changes were found.')
        ->assertFailed();
});
