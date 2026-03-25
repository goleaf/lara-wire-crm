<?php

namespace Modules\Deals\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Deals\Models\Deal;
use Modules\Deals\Models\PipelineStage;
use Modules\Deals\Services\DealService;

class DealsController extends Controller
{
    public function __construct(
        protected DealService $dealService
    ) {}

    public function stage(Request $request, string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('deals.edit'), 403);

        $validated = $request->validate([
            'stage_id' => ['required', 'uuid', 'exists:pipeline_stages,id'],
        ]);

        $deal = Deal::query()->findOrFail($id);
        $stage = PipelineStage::query()->findOrFail($validated['stage_id']);

        $this->dealService->moveToStage($deal, $stage);

        return back()->with('status', 'Deal stage updated.');
    }

    public function won(string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('deals.edit'), 403);

        $deal = Deal::query()->findOrFail($id);
        $this->dealService->markWon($deal);

        return back()->with('status', 'Deal marked as won.');
    }

    public function lost(Request $request, string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('deals.edit'), 403);

        $validated = $request->validate([
            'lost_reason' => ['required', 'in:Price,Competitor,No Budget,No Decision,Other'],
            'lost_notes' => ['nullable', 'string'],
        ]);

        $deal = Deal::query()->findOrFail($id);
        $this->dealService->markLost($deal, $validated['lost_reason'], $validated['lost_notes'] ?? null);

        return back()->with('status', 'Deal marked as lost.');
    }

    public function destroy(string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('deals.delete'), 403);

        Deal::query()->whereKey($id)->delete();

        return redirect()->route('deals.index')->with('status', 'Deal deleted.');
    }
}
