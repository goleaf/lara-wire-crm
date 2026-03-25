<?php

use App\Support\Git\CommitMessageBuilder;

test('builds a commit message with full file details', function () {
    $builder = new CommitMessageBuilder();

    $message = $builder->build([
        [
            'status' => 'M',
            'path' => 'app/Models/User.php',
            'additions' => 12,
            'deletions' => 4,
        ],
        [
            'status' => 'A',
            'path' => 'app/Support/Git/CommitMessageBuilder.php',
            'additions' => 48,
            'deletions' => 0,
        ],
        [
            'status' => 'D',
            'path' => 'tests/Feature/OldTest.php',
            'additions' => 0,
            'deletions' => 17,
        ],
    ]);

    expect($message)
        ->toContain('chore(auto): update 3 files (+60/-21)')
        ->toContain('Status summary:')
        ->toContain('- Added: 1')
        ->toContain('- Modified: 1')
        ->toContain('- Deleted: 1')
        ->toContain('Changed files:')
        ->toContain('- M app/Models/User.php (+12/-4)')
        ->toContain('- A app/Support/Git/CommitMessageBuilder.php (+48/-0)')
        ->toContain('- D tests/Feature/OldTest.php (+0/-17)');
});

test('uses raw counts for binary or unknown diffs', function () {
    $builder = new CommitMessageBuilder();

    $message = $builder->build([
        [
            'status' => 'M',
            'path' => 'public/logo.png',
            'additions' => '-',
            'deletions' => '-',
        ],
    ]);

    expect($message)
        ->toContain('chore(auto): update 1 file')
        ->toContain('- M public/logo.png (+-/--)');
});
