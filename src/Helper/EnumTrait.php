<?php

namespace Flyokai\DataMate\Helper;

trait EnumTrait
{
    public static function allowedValues(): array
    {
        static $allowedValues = null;
        return $allowedValues ??= array_column(self::cases(), 'value');
    }

    public static function allowedNames(): array
    {
        static $allowedNames = null;
        return $allowedNames ??= array_column(self::cases(), 'name');
    }

    public static function isAllowed(string|int $value): bool
    {
        return in_array($value, self::allowedValues());
    }

    public static function isAllowedName(string $value): bool
    {
        return in_array($value, self::allowedNames());
    }

    public static function fromName(string $name): static
    {
        foreach (self::cases() as $status) {
            if ($name === $status->name){
                return $status;
            }
        }
        throw new \ValueError("$name is not a valid backing value for enum " . self::class );
    }

    public static function tryFromName(string $name): static|null
    {
        try {
            return self::fromName($name);
        } catch (\ValueError) {
            return null;
        }
    }

    public function name(): string
    {
        return $this->name;
    }

}
