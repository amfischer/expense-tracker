<?php

namespace App\Enums;

use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Names;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;

enum Currency: string
{
    use InvokableCases, Names, Options, Values;

    case USD = 'USD';
    // case PEN = 'PEN';

    public static function HTMLSelectOptions(): array
    {
        $options = [];

        foreach (self::cases() as $paymentMethod) {
            $options[] = [
                'id'   => $paymentMethod->value,
                'name' => $paymentMethod->value,
            ];
        }

        return $options;
    }
}
