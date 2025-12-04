<?php

namespace Flyokai\DataMate;

interface HasAltId
{
    public function altId(string|array $altIdKey): int|string|array|null;
    public static function altIdKeys(): array;
    public static function hasAltIdKey(string|array $altIdKey): bool;
    public static function assetAltIdKey(array|string $altIdKey): void;
}
