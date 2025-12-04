<?php

namespace Flyokai\DataMate;

interface HasId
{
    public function id(): int|string|null;
    public static function idKey(): string;
    public static function entityType(): string;
}
