<?php

namespace Modules\Campaigns\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Campaigns\Models\Campaign;

class CampaignsController extends Controller
{
    public function addContacts(Request $request, string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('campaigns.create'), 403);

        $validated = $request->validate([
            'contact_ids' => ['required', 'array', 'min:1'],
            'contact_ids.*' => ['uuid', 'exists:contacts,id'],
            'status' => ['nullable', 'in:Targeted,Contacted,Responded,Converted,Opted Out'],
        ]);

        $campaign = Campaign::query()->findOrFail($id);
        $status = $validated['status'] ?? 'Targeted';

        $syncPayload = [];

        foreach ($validated['contact_ids'] as $contactId) {
            $syncPayload[$contactId] = [
                'added_at' => now(),
                'status' => $status,
            ];
        }

        $campaign->contacts()->syncWithoutDetaching($syncPayload);

        return back()->with('status', 'Contacts added to campaign.');
    }

    public function removeContact(string $id, string $contactId): RedirectResponse
    {
        abort_unless(auth()->user()?->can('campaigns.delete'), 403);

        $campaign = Campaign::query()->findOrFail($id);
        $campaign->contacts()->detach($contactId);

        return back()->with('status', 'Contact removed from campaign.');
    }

    public function destroy(string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('campaigns.delete'), 403);

        Campaign::query()->whereKey($id)->delete();

        return redirect()
            ->route('campaigns.index')
            ->with('status', 'Campaign deleted.');
    }
}
