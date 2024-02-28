<?php

namespace App\Enums;

use ArchTech\Enums\Names;
use ArchTech\Enums\Values;
use ArchTech\Enums\Options;
use ArchTech\Enums\InvokableCases;

enum Currency: string
{
    use InvokableCases, Names, Values, Options;

    case USD = 'USD';
    // case PEN = 'PEN';

}
