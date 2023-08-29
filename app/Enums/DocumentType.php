<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class DocumentType extends Enum implements LocalizedEnum
{
    public const IMPORT = 1;

    public const EXPORT = 2;
}
