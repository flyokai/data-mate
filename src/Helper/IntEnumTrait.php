<?php

namespace Flyokai\DataMate\Helper;

use Flyokai\DataMate\ValidationException;

trait IntEnumTrait
{
    use EnumTrait;

    public static function validate(string|int|null $value): string
    {
        Assertions::assertNotNull($value);
        if (!is_numeric($value) || !self::isAllowed((int)$value)) {
            throw new ValidationException(sprintf(
                'Invalid enum value. Should be one of: %s',
                implode(',', self::allowedValues())
            ));
        }
        return $value;
    }
}
