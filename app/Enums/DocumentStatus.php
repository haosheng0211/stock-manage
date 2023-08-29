<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class DocumentStatus extends Enum implements LocalizedEnum
{
    public const PROCESS = 1;

    public const SUCCESS = 2;

    public const FAILURE = 3;

    public static function colors(): array
    {
        return [
            'success' => self::SUCCESS,
            'warning' => self::FAILURE,
        ];
    }
}
