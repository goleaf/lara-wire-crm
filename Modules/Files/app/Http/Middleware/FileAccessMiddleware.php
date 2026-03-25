<?php

namespace Modules\Files\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Files\Models\CrmFile;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class FileAccessMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response|SymfonyResponse
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $id = (string) ($request->route('id') ?? '');

        if ($id === '') {
            return $next($request);
        }

        $file = CrmFile::query()->findOrFail($id);

        if ($file->uploaded_by === $user->getKey()) {
            return $next($request);
        }

        if ($file->related_to_type && ! class_exists($file->related_to_type)) {
            abort(403);
        }

        $relatedRecord = $file->relatedTo;

        if ($relatedRecord && $user->hasRecordAccess($relatedRecord)) {
            return $next($request);
        }

        abort(403);
    }
}
