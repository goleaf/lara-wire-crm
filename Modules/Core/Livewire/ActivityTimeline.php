<?php

namespace Modules\Core\Livewire;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Defer;
use Livewire\Component;
use Modules\Activities\Models\Activity;
use Modules\Cases\Models\CaseComment;
use Modules\Cases\Models\SupportCase;
use Modules\Core\Models\AuditLog;
use Modules\Files\Models\CrmFile;

#[Defer]
class ActivityTimeline extends Component
{
    public string $modelType = '';

    public string $modelId = '';

    public function mount(string $modelType, string $modelId): void
    {
        abort_unless(auth()->check(), 403);

        $this->modelType = $modelType;
        $this->modelId = $modelId;
    }

    public function render(): View
    {
        $items = collect()
            ->merge($this->loadActivities())
            ->merge($this->loadComments())
            ->merge($this->loadAuditLogs())
            ->merge($this->loadFiles())
            ->sortByDesc('date')
            ->values();

        return view('core::livewire.activity-timeline', [
            'items' => $items,
        ]);
    }

    /**
     * @return Collection<int, array{type: string, title: string, body: string, date: Carbon|string|null}>
     */
    protected function loadActivities(): Collection
    {
        if (! class_exists(Activity::class)) {
            return collect();
        }

        return Activity::query()
            ->select(['id', 'type', 'subject', 'description', 'created_at', 'related_to_type', 'related_to_id'])
            ->where('related_to_type', $this->modelType)
            ->where('related_to_id', $this->modelId)
            ->limit(50)
            ->get()
            ->map(fn ($activity): array => [
                'type' => 'activity',
                'title' => $activity->subject,
                'body' => (string) ($activity->description ?? ''),
                'date' => $activity->created_at,
            ]);
    }

    /**
     * @return Collection<int, array{type: string, title: string, body: string, date: Carbon|string|null}>
     */
    protected function loadComments(): Collection
    {
        if ($this->modelType !== SupportCase::class || ! class_exists(CaseComment::class)) {
            return collect();
        }

        return CaseComment::query()
            ->select(['id', 'case_id', 'body', 'is_internal', 'created_at'])
            ->where('case_id', $this->modelId)
            ->limit(100)
            ->get()
            ->map(fn ($comment): array => [
                'type' => 'comment',
                'title' => $comment->is_internal ? 'Internal Comment' : 'Comment',
                'body' => (string) $comment->body,
                'date' => $comment->created_at,
            ]);
    }

    /**
     * @return Collection<int, array{type: string, title: string, body: string, date: Carbon|string|null}>
     */
    protected function loadAuditLogs(): Collection
    {
        if (! class_exists(AuditLog::class)) {
            return collect();
        }

        return AuditLog::query()
            ->select(['id', 'action', 'model_type', 'model_id', 'created_at'])
            ->where('model_type', $this->modelType)
            ->where('model_id', $this->modelId)
            ->limit(100)
            ->get()
            ->map(fn (AuditLog $auditLog): array => [
                'type' => 'audit',
                'title' => ucfirst($auditLog->action),
                'body' => 'Record '.$auditLog->action,
                'date' => $auditLog->created_at,
            ]);
    }

    /**
     * @return Collection<int, array{type: string, title: string, body: string, date: Carbon|string|null}>
     */
    protected function loadFiles(): Collection
    {
        if (! class_exists(CrmFile::class)) {
            return collect();
        }

        return CrmFile::query()
            ->select(['id', 'name', 'related_to_type', 'related_to_id', 'created_at'])
            ->where('related_to_type', $this->modelType)
            ->where('related_to_id', $this->modelId)
            ->limit(50)
            ->get()
            ->map(fn ($file): array => [
                'type' => 'file',
                'title' => 'File uploaded',
                'body' => (string) $file->name,
                'date' => $file->created_at,
            ]);
    }
}
