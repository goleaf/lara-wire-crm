<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Modules\Files\Livewire\FileUploadZone;
use Modules\Files\Models\CrmFile;
use Modules\Users\Models\Role;

function makeFilesRole(): Role
{
    return Role::query()->create([
        'name' => 'Files Admin '.str()->random(6),
        'can_view' => true,
        'can_create' => true,
        'can_edit' => true,
        'can_delete' => true,
        'can_export' => true,
        'record_visibility' => 'all',
        'module_access' => ['files' => true, 'users' => true, 'core' => true],
    ]);
}

test('authorized users can open file manager page', function () {
    $role = makeFilesRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user)
        ->get(route('files.index'))
        ->assertOk()
        ->assertSee('File Manager');
});

test('users can upload files through livewire upload zone', function () {
    Storage::fake('local');

    $role = makeFilesRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user);

    Livewire::test(FileUploadZone::class)
        ->set('uploads', [UploadedFile::fake()->image('customer-logo.png')])
        ->call('save');

    $file = CrmFile::query()->first();

    expect($file)->not->toBeNull();
    expect($file->storage_path)->toStartWith('crm-files/');
    Storage::disk('local')->assertExists($file->storage_path);
});

test('authorized users can download uploaded files', function () {
    Storage::fake('local');

    $role = makeFilesRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    Storage::disk('local')->put('crm-files/contracts/sample-contract.txt', 'contract body');

    $file = CrmFile::query()->create([
        'name' => 'Sample Contract',
        'original_filename' => 'sample-contract.txt',
        'mime_type' => 'text/plain',
        'extension' => 'txt',
        'size_bytes' => 13,
        'disk' => 'local',
        'storage_path' => 'crm-files/contracts/sample-contract.txt',
        'uploaded_by' => $user->id,
        'version' => 1,
        'is_public' => false,
    ]);

    $this->actingAs($user)
        ->get(route('files.download', $file->id))
        ->assertDownload('sample-contract.txt');
});
