<?php

namespace Modules\Calendar\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\Calendar\Services\CalendarService;

class CalendarController extends Controller
{
    public function events(Request $request, CalendarService $calendarService): JsonResponse
    {
        abort_unless(auth()->user()?->can('calendar.view'), 403);

        $validated = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        $from = Carbon::parse($validated['from'])->startOfDay();
        $to = Carbon::parse($validated['to'])->endOfDay();

        $events = $calendarService
            ->getEventsForMonth($from->year, $from->month, auth()->user())
            ->filter(fn ($event): bool => $event->start_at->between($from, $to))
            ->values()
            ->map(fn ($event): array => [
                'id' => $event->id,
                'title' => $event->title,
                'type' => $event->type,
                'start_at' => $event->start_at?->toDateTimeString(),
                'end_at' => $event->end_at?->toDateTimeString(),
                'all_day' => $event->all_day,
                'status' => $event->status,
                'color' => $event->color,
                'organizer' => $event->organizer?->full_name,
            ])
            ->all();

        return response()->json([
            'data' => $events,
        ]);
    }
}
