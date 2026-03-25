<?php

namespace Modules\Files\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Modules\Files\Models\CrmFile;
use Modules\Files\Services\FileService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    public function download(string $id, FileService $fileService): StreamedResponse
    {
        $file = CrmFile::query()->findOrFail($id);

        return $fileService->download($file);
    }

    public function preview(string $id): BinaryFileResponse|Response
    {
        $file = CrmFile::query()->findOrFail($id);
        $disk = Storage::disk($file->disk);

        if (! $disk->exists($file->storage_path)) {
            abort(404);
        }

        $path = $disk->path($file->storage_path);

        if (! $file->is_image && ! $file->is_pdf) {
            return response()->download($path, $file->original_filename);
        }

        return response()->file($path, [
            'Content-Type' => $file->mime_type,
            'Content-Disposition' => 'inline; filename="'.$file->original_filename.'"',
        ]);
    }

    public function rename(Request $request, string $id): Response|SymfonyResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $file = CrmFile::query()->findOrFail($id);
        $file->name = $validated['name'];
        $file->save();

        return redirect()
            ->back()
            ->with('status', 'File renamed.');
    }

    public function destroy(string $id, FileService $fileService): Response|SymfonyResponse
    {
        $file = CrmFile::query()->findOrFail($id);
        $fileService->delete($file);

        return redirect()
            ->back()
            ->with('status', 'File removed.');
    }
}
