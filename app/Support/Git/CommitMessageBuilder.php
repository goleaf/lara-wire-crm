<?php

namespace App\Support\Git;

class CommitMessageBuilder
{
    /**
     * @param  array<int, array{status: string, path: string, additions: int|string|null, deletions: int|string|null}>  $changes
     */
    public function build(array $changes): string
    {
        $fileCount = count($changes);
        $fileLabel = $fileCount === 1 ? 'file' : 'files';

        $totalAdditions = 0;
        $totalDeletions = 0;

        $statusSummary = [
            'Added' => 0,
            'Modified' => 0,
            'Deleted' => 0,
            'Renamed' => 0,
            'Copied' => 0,
            'TypeChanged' => 0,
            'Unmerged' => 0,
            'Unknown' => 0,
        ];

        foreach ($changes as $change) {
            $statusLabel = $this->statusLabel($change['status']);
            $statusSummary[$statusLabel]++;

            if (is_int($change['additions'])) {
                $totalAdditions += $change['additions'];
            }

            if (is_int($change['deletions'])) {
                $totalDeletions += $change['deletions'];
            }
        }

        $title = sprintf('chore(auto): update %d %s', $fileCount, $fileLabel);

        if ($totalAdditions > 0 || $totalDeletions > 0) {
            $title .= sprintf(' (+%d/-%d)', $totalAdditions, $totalDeletions);
        }

        $lines = [
            $title,
            '',
            'Status summary:',
        ];

        foreach ($statusSummary as $label => $count) {
            if ($count === 0) {
                continue;
            }

            $lines[] = sprintf('- %s: %d', $label, $count);
        }

        $lines[] = '';
        $lines[] = 'Changed files:';

        foreach ($changes as $change) {
            $lines[] = sprintf(
                '- %s %s (+%s/-%s)',
                $change['status'],
                $change['path'],
                $this->formatDelta($change['additions']),
                $this->formatDelta($change['deletions']),
            );
        }

        return implode(PHP_EOL, $lines);
    }

    private function statusLabel(string $status): string
    {
        return match (substr($status, 0, 1)) {
            'A' => 'Added',
            'M' => 'Modified',
            'D' => 'Deleted',
            'R' => 'Renamed',
            'C' => 'Copied',
            'T' => 'TypeChanged',
            'U' => 'Unmerged',
            default => 'Unknown',
        };
    }

    private function formatDelta(int|string|null $value): string
    {
        if (is_int($value)) {
            return (string) $value;
        }

        if (is_string($value) && $value !== '') {
            return $value;
        }

        return '?';
    }
}
