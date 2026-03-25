<?php

namespace Modules\Core\Enums;

enum CurrencySymbol: string
{
    case Dollar = '$';
    case Euro = '€';
    case Pound = '£';
    case Yen = '¥';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $symbol): string => $symbol->value,
            self::cases(),
        );
    }
}
