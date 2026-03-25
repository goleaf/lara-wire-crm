<?php

namespace Modules\Files\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Modules\Files\Models\CrmFile;

class CrmFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::query()->select(['id'])->limit(5)->pluck('id')->all();

        if ($users === []) {
            return;
        }

        $seededFiles = [
            [
                'name' => 'Sales Contract',
                'original_filename' => 'sales-contract.txt',
                'mime_type' => 'text/plain',
                'extension' => 'txt',
                'size_bytes' => 2048,
                'storage_path' => 'crm-files/contracts/sales-contract.txt',
                'description' => 'Primary signed sales contract.',
                'uploaded_by' => (string) $users[0],
            ],
            [
                'name' => 'Account Blueprint',
                'original_filename' => 'account-blueprint.pdf',
                'mime_type' => 'application/pdf',
                'extension' => 'pdf',
                'size_bytes' => 4096,
                'storage_path' => 'crm-files/docs/account-blueprint.pdf',
                'description' => 'Account setup blueprint document.',
                'uploaded_by' => (string) $users[array_rand($users)],
            ],
        ];

        foreach ($seededFiles as $fileData) {
            Storage::disk('local')->put($fileData['storage_path'], $fileData['name'].' generated for seeded CRM data.');

            $file = CrmFile::query()->updateOrCreate(
                ['storage_path' => $fileData['storage_path']],
                [
                    'name' => $fileData['name'],
                    'original_filename' => $fileData['original_filename'],
                    'mime_type' => $fileData['mime_type'],
                    'extension' => $fileData['extension'],
                    'size_bytes' => $fileData['size_bytes'],
                    'disk' => 'local',
                    'uploaded_by' => $fileData['uploaded_by'],
                    'related_to_type' => User::class,
                    'related_to_id' => $fileData['uploaded_by'],
                    'description' => $fileData['description'],
                    'version' => 1,
                    'parent_file_id' => null,
                    'is_public' => false,
                ]
            );

            $versionPath = str_replace('.'.$fileData['extension'], '-v2.'.$fileData['extension'], $fileData['storage_path']);

            Storage::disk('local')->put($versionPath, $fileData['name'].' version 2 generated for seeded CRM data.');

            CrmFile::query()->updateOrCreate(
                ['storage_path' => $versionPath],
                [
                    'name' => $fileData['name'].' v2',
                    'original_filename' => str_replace('.'.$fileData['extension'], '-v2.'.$fileData['extension'], $fileData['original_filename']),
                    'mime_type' => $fileData['mime_type'],
                    'extension' => $fileData['extension'],
                    'size_bytes' => $fileData['size_bytes'] + 512,
                    'disk' => 'local',
                    'uploaded_by' => $fileData['uploaded_by'],
                    'related_to_type' => User::class,
                    'related_to_id' => $fileData['uploaded_by'],
                    'description' => $fileData['description'].' Version 2.',
                    'version' => 2,
                    'parent_file_id' => (string) $file->getKey(),
                    'is_public' => false,
                ]
            );
        }
    }
}
