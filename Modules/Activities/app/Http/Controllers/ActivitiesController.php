<?php

namespace Modules\Activities\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Activities\Models\Activity;

class ActivitiesController extends Controller
{
    public function complete(string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('activities.edit'), 403);

        Activity::query()
            ->whereKey($id)
            ->update([
                'status' => 'Completed',
                'completed_at' => now(),
            ]);

        return back()->with('status', 'Activity marked as completed.');
    }

    public function cancel(string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('activities.edit'), 403);

        Activity::query()
            ->whereKey($id)
            ->update([
                'status' => 'Cancelled',
            ]);

        return back()->with('status', 'Activity cancelled.');
    }

    public function destroy(string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('activities.delete'), 403);

        Activity::query()->whereKey($id)->delete();

        return redirect()
            ->route('activities.index')
            ->with('status', 'Activity deleted.');
    }
}
