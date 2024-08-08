<?php

namespace App\Enums;

use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Names;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;

enum PaymentMethod: string
{
    use InvokableCases, Names, Options, Values;

    case NONE = 'none';
    case WELLS_FARGO_VISA = 'wells-fargo-visa';
    case DISCOVER_CARD = 'discover-card';
    case CHARLES_SCHWAB_CHECKING = 'charles-schwab-checking';
    case BCP_TRANSFER = 'bcp-transfer';

    // extra helper to allow for greater customization of displayed values, without disclosing the name/value data directly
    public function label(): string
    {
        return match ($this) {
            self::NONE                    => 'None',
            self::WELLS_FARGO_VISA        => 'Wells Fargo Visa',
            self::DISCOVER_CARD           => 'Discover Card',
            self::CHARLES_SCHWAB_CHECKING => 'Charles Schwab Checking',
            self::BCP_TRANSFER            => 'BCP Transfer',
        };
    }

    public static function HTMLSelectOptions(): array
    {
        $options = [];

        foreach (self::cases() as $paymentMethod) {
            $options[] = [
                'id'   => $paymentMethod->value,
                'name' => $paymentMethod->label(),
            ];
        }

        return $options;
    }
}
