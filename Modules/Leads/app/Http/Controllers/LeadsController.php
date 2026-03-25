<?php

namespace Modules\Leads\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Leads\Models\Lead;

class LeadsController extends Controller
{
    public function convert(string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('leads.edit'), 403);

        $lead = Lead::query()->findOrFail($id);
        $result = $lead->convert(auth()->user());

        return redirect()
            ->route('leads.show', $lead->id)
            ->with('status', 'Lead converted successfully. Contact: '.$result['contact']->full_name);
    }

    public function destroy(string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('leads.delete'), 403);

        Lead::query()->whereKey($id)->delete();

        return redirect()
            ->route('leads.index')
            ->with('status', 'Lead deleted.');
    }
}
