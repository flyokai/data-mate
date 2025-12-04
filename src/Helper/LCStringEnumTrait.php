<?php

namespace Flyokai\DataMate\Helper;

trait LCStringEnumTrait
{
    use StringEnumTrait {
        normalize as traitNormalize;
    }

    public static function normalize(string $value): string
    {
        return strtolower($value);
    }

}
