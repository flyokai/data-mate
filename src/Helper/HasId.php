<?php

namespace Flyokai\DataMate\Helper;

trait HasId
{
    public function id(): int|string|null
    {
        return $this->{static::idKey()};
    }
    public static function entityType(): string
    {
        return static::$type;
    }
    public static function idKey(): string
    {
        return static::$idKey;
    }
    public function extractIdentity(): int|string|array|null
    {
        if ($this->id()) {
            return $this->id();
        }
        $entityId = [];
        if ($this instanceof \Flyokai\DataMate\HasAltId) {
            foreach (static::altIdKeys() as $altIdKey) {
                if (!($__altId = $this->altId($altIdKey))) continue;
                if (!is_array($__altId)) {
                    $__altId = [$altIdKey=>$__altId];
                }
                $entityId = array_merge($entityId, $__altId);
            }
        }
        return !empty($entityId) ? $entityId : null;
    }
}
