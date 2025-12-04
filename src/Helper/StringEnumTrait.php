<?php

namespace Flyokai\DataMate\Helper;

use Flyokai\DataMate\ValidationException;

trait StringEnumTrait
{
    use EnumTrait;

    public static function validate(string|null $value): string
    {
        Assertions::assertNotNull($value);
        if (!self::isAllowed($value)) {
            throw new ValidationException(sprintf(
                'Invalid enum value. Should be one of: %s',
                implode(',', self::allowedValues())
            ));
        }
        return $value;
    }

    public static function fromValue(string $value): static
    {
        $value = self::normalize($value);
        foreach (self::cases() as $status) {
            if ($value === $status->value){
                return $status;
            }
        }
        throw new \ValueError("$value is not a valid backing value for enum " . self::class );
    }

    public static function tryFromValue(string $value): static|null
    {
        try {
            return self::fromValue($value);
        } catch (\ValueError) {
            return null;
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public static function normalize(string $value): string
    {
        return $value;
    }
}
