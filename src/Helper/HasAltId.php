<?php

namespace Flyokai\DataMate\Helper;

use Flyokai\DataMate\InvalidEntity;

trait HasAltId
{
    public static function assetAltIdKey(array|string $altIdKey): void
    {
        if (!static::hasAltIdKey($altIdKey)) {
            throw InvalidEntity::unrecognizedAltIdKey(
                static::entityType(),
                $altIdKey
            );
        }
    }
    public static function normalizeAltIdKey(array|string $altIdKey): array|string
    {
        return \Flyokai\DataMate\normalizeAltIdKey($altIdKey);
    }
    public static function hasAltIdKey(array|string $altIdKey): bool
    {
        $altIdKeys = static::altIdKeys();
        $altIdKey = self::normalizeAltIdKey($altIdKey);
        return in_array($altIdKey, $altIdKeys);
    }
    public function altId(string|array $altIdKey): int|string|array|null
    {
        $altIdKey = self::normalizeAltIdKey($altIdKey);
        self::assetAltIdKey($altIdKey);
        return \Flyokai\DataMate\fetchAltId($altIdKey, $this);
    }
    protected static function prepareAltIdKeys(array $altIdKeys): array
    {
        return \Flyokai\DataMate\prepareAltIdKeys($altIdKeys, static::class);
    }
}
