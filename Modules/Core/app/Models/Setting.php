<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Database\Factories\SettingFactory;

class Setting extends BaseModel
{
    use HasFactory;

    private const string CACHE_KEY = 'crm.settings.values';

    protected $table = 'settings';

    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'string',
        ];
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $settings = static::allValues();

        return $settings[$key] ?? $default;
    }

    /**
     * @return array<string, mixed>
     */
    public static function allValues(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addMinutes(10), function (): array {
            return static::query()
                ->select(['key', 'value', 'type'])
                ->get()
                ->mapWithKeys(fn (self $setting): array => [
                    $setting->key => static::castStoredValue($setting->value, $setting->type),
                ])
                ->all();
        });
    }

    public static function setValue(string $key, mixed $value, string $type = 'string'): void
    {
        $encodedValue = match ($type) {
            'json' => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };

        static::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => $encodedValue,
                'type' => $type,
            ]
        );

        Cache::forget(self::CACHE_KEY);
    }

    private static function castStoredValue(?string $value, string $type): mixed
    {
        return match ($type) {
            'integer' => (int) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json' => is_array($decoded = json_decode((string) $value, true)) ? $decoded : [],
            default => $value,
        };
    }

    protected static function newFactory(): SettingFactory
    {
        return SettingFactory::new();
    }
}
