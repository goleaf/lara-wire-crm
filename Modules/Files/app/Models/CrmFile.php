<?php

namespace Modules\Files\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Models\BaseModel;
use Modules\Files\Services\FileService;

class CrmFile extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'files';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'original_filename',
        'mime_type',
        'extension',
        'size_bytes',
        'disk',
        'storage_path',
        'uploaded_by',
        'related_to_type',
        'related_to_id',
        'description',
        'version',
        'parent_file_id',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
            'version' => 'integer',
            'is_public' => 'boolean',
        ];
    }

    public function relatedTo(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_file_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(self::class, 'parent_file_id')->orderByDesc('version');
    }

    public function getSizeFormattedAttribute(): string
    {
        $size = max(0, (int) $this->size_bytes);
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = $size > 0 ? (int) floor(log($size, 1024)) : 0;
        $power = min($power, count($units) - 1);

        $scaled = $size / (1024 ** $power);
        $precision = $power === 0 ? 0 : 2;

        return number_format($scaled, $precision).' '.$units[$power];
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->storage_path);
    }

    public function getIsImageAttribute(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }

    public function getIsPdfAttribute(): bool
    {
        return (string) $this->mime_type === 'application/pdf';
    }

    public function getIconAttribute(): string
    {
        return match (strtolower((string) $this->extension)) {
            'pdf' => 'file-text',
            'doc', 'docx' => 'file-type',
            'xls', 'xlsx', 'csv' => 'table',
            'jpg', 'jpeg', 'png', 'gif', 'webp' => 'image',
            'zip', 'rar' => 'archive',
            default => 'file',
        };
    }

    public function scopeForRecord(Builder $query, string $type, string $id): Builder
    {
        return $query
            ->where('related_to_type', $type)
            ->where('related_to_id', $id);
    }

    public function scopeImages(Builder $query): Builder
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    public function scopeDocuments(Builder $query): Builder
    {
        return $query->where(function (Builder $inner): void {
            $inner
                ->where('mime_type', 'application/pdf')
                ->orWhereIn('extension', ['doc', 'docx', 'xls', 'xlsx', 'csv', 'txt']);
        });
    }

    /**
     * @param  array{
     *     uploaded_by: User|string,
     *     related_to_type?: string|null,
     *     related_to_id?: string|null
     * }  $data
     */
    public static function upload(UploadedFile $file, array $data): self
    {
        Validator::validate([
            'file' => $file,
        ], [
            'file' => ['required', 'file', 'max:'.((int) config('files.max_size_kb', 10240))],
        ]);

        $uploader = $data['uploaded_by'] instanceof User
            ? $data['uploaded_by']
            : User::query()->findOrFail((string) $data['uploaded_by']);

        return app(FileService::class)->store(
            $file,
            $data['related_to_type'] ?? null,
            $data['related_to_id'] ?? null,
            $uploader
        );
    }
}
