<?php

namespace Flyokai\DataMate;

class InvalidEntity extends \InvalidArgumentException
{
    public static function missingId(string $entityType): self
    {
        return new self(sprintf(
            'Missing id for entity "%s".',
            $entityType
        ));
    }

    public static function altIdKeyNotSupported(string $entityType): self
    {
        return new self(sprintf(
            'Alternative id-key (string type) for "%s" is not supported.',
            $entityType
        ));
    }

    public static function unrecognizedAltIdKey(string $entityType, string|array $altIdKey): self
    {
        $altIdKey = is_array($altIdKey) ? var_export($altIdKey, true) : $altIdKey;
        return new self(sprintf(
            '"%s" does not recognize alternative id-key: %s',
            $entityType,
            $altIdKey
        ));
    }

    public static function altIdKeyNoMatch(string $entityType, int|string|array $entityId): self
    {
        $entityId = is_array($entityId) ? var_export($entityId, true) : $entityId;
        return new self(sprintf(
            '"%s" does not have alternative id-key that match this input: %s',
            $entityType,
            $entityId
        ));
    }

    public static function ambiguous(string $entityType, ?string $criteria=null): self
    {
        if ($criteria === null) {
            return new self(sprintf(
                'Multiple "%s" entities found.',
                $entityType
            ));
        } else {
            return new self(sprintf(
                'Multiple "%s" entities found for the search criteria (%s).',
                $entityType, $criteria
            ));
        }
    }
}
