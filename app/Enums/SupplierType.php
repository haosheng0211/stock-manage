<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class SupplierType extends Enum implements LocalizedEnum
{
    public const NONE = 0;

    public const ORIGINAL = 1;

    public const AGENT = 2;

    public const TRADER = 3;

    public const FACTORY = 4;
}
