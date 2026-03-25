<?php

namespace Modules\Files\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Files\Models\CrmFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileService
{
    public function store(
        UploadedFile $file,
        ?string $relatedType,
        ?string $relatedId,
        User $user
    ): CrmFile {
        $directory = trim((string) config('files.storage_directory', 'crm-files'), '/');
        $storedPath = $file->store($directory, 'local');
        $originalFilename = (string) $file->getClientOriginalName();
        $baseName = pathinfo($originalFilename, PATHINFO_FILENAME);
        $extension = strtolower((string) ($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin'));

        return CrmFile::query()->create([
            'name' => $baseName,
            'original_filename' => $originalFilename,
            'mime_type' => (string) ($file->getClientMimeType() ?: $file->getMimeType() ?: 'application/octet-stream'),
            'extension' => $extension,
            'size_bytes' => $file->getSize() ?: 0,
            'disk' => 'local',
            'storage_path' => $storedPath,
            'uploaded_by' => $user->getKey(),
            'related_to_type' => $relatedType,
            'related_to_id' => $relatedId,
            'version' => 1,
            'is_public' => false,
        ]);
    }

    public function storeVersion(UploadedFile $file, CrmFile $parent, User $user): CrmFile
    {
        $newVersion = $this->store($file, $parent->related_to_type, $parent->related_to_id, $user);

        $latestVersion = (int) max(
            $parent->version,
            (int) $parent->versions()->max('version')
        );

        $newVersion->forceFill([
            'name' => $parent->name,
            'parent_file_id' => $parent->id,
            'version' => $latestVersion + 1,
        ])->save();

        return $newVersion;
    }

    public function delete(CrmFile $file): void
    {
        if ((bool) config('files.delete_from_disk_on_soft_delete', false)) {
            Storage::disk($file->disk)->delete($file->storage_path);
        }

        $file->delete();
    }

    public function download(CrmFile $file): StreamedResponse
    {
        return Storage::disk($file->disk)->download(
            $file->storage_path,
            $file->original_filename
        );
    }

    public function generateThumbnail(CrmFile $file): ?string
    {
        if (! $file->is_image) {
            return null;
        }

        $disk = Storage::disk($file->disk);

        if (! $disk->exists($file->storage_path)) {
            return null;
        }

        $binary = $disk->get($file->storage_path);
        $image = @imagecreatefromstring($binary);

        if (! $image) {
            return null;
        }

        $thumb = imagescale($image, 320);
        $directory = trim((string) config('files.storage_directory', 'crm-files'), '/').'/thumbnails';
        $thumbnailPath = $directory.'/'.$file->id.'.jpg';

        ob_start();
        imagejpeg($thumb ?: $image, null, 85);
        $thumbnailBinary = ob_get_clean();

        if ($thumbnailBinary !== false) {
            $disk->put($thumbnailPath, $thumbnailBinary);
        }

        imagedestroy($image);

        if ($thumb && $thumb !== $image) {
            imagedestroy($thumb);
        }

        return $thumbnailPath;
    }
}
