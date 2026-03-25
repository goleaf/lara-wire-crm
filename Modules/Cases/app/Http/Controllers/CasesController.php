<?php

namespace Modules\Cases\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Cases\Models\SupportCase;

class CasesController extends Controller
{
    public function addComment(Request $request, string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('cases.create'), 403);

        $validated = $request->validate([
            'body' => ['required', 'string'],
            'is_internal' => ['nullable', 'boolean'],
        ]);

        $supportCase = SupportCase::query()
            ->select(['id'])
            ->findOrFail($id);

        $supportCase->comments()->create([
            'user_id' => auth()->id(),
            'body' => $validated['body'],
            'is_internal' => (bool) ($validated['is_internal'] ?? false),
        ]);

        return back()->with('status', 'Comment added.');
    }

    public function updateStatus(Request $request, string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('cases.edit'), 403);

        $validated = $request->validate([
            'status' => ['required', 'in:Open,In Progress,Pending,Resolved,Closed'],
        ]);

        $supportCase = SupportCase::query()
            ->select(['id', 'status', 'resolved_at', 'closed_at'])
            ->findOrFail($id);

        $supportCase->status = $validated['status'];
        $supportCase->save();

        return back()->with('status', 'Case status updated.');
    }

    public function updateSatisfaction(Request $request, string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('cases.edit'), 403);

        $validated = $request->validate([
            'satisfaction_score' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string'],
        ]);

        $supportCase = SupportCase::query()
            ->select(['id', 'resolution_notes'])
            ->findOrFail($id);

        $supportCase->forceFill([
            'satisfaction_score' => $validated['satisfaction_score'],
            'resolution_notes' => filled($validated['comment'] ?? null)
                ? trim((string) $supportCase->resolution_notes."\n\nCSAT: ".$validated['comment'])
                : $supportCase->resolution_notes,
        ])->save();

        return back()->with('status', 'Satisfaction saved.');
    }

    public function destroy(string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('cases.delete'), 403);

        SupportCase::query()->whereKey($id)->delete();

        return redirect()
            ->route('cases.index')
            ->with('status', 'Case deleted.');
    }
}
