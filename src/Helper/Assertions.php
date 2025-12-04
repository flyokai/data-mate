<?php

namespace Flyokai\DataMate\Helper;

use Flyokai\DataMate\ValidationException;

class Assertions
{
    protected static function createValidationException($message)
    {
        return new ValidationException($message);
    }
    public static function assertNotNull(string|null $value, ?string $name=null): void
    {
        if (is_null($value)) {
            if ($name !== null) {
                throw static::createValidationException(sprintf('Value for "%s" should not be null', $name));
            } else {
                throw static::createValidationException('Value should not be null');
            }
        }
    }

    public static function assertPositiveInt(int|string|null $value, ?string $name=null): void
    {
        if (!is_numeric($value) || $value<=0 || intval($value)!=$value) {
            if ($name !== null) {
                throw static::createValidationException(sprintf('Value for "%s" should be positive integer', $name));
            } else {
                throw static::createValidationException('Value should be positive integer');
            }
        }
    }

    public static function assertPositiveFloat(float|int|string|null $value, ?string $name=null): void
    {
        if (!is_numeric($value) || $value<=0) {
            if ($name !== null) {
                throw static::createValidationException(sprintf('Value for "%s" should be positive float', $name));
            } else {
                throw static::createValidationException('Value should be positive float');
            }
        }
    }
}
