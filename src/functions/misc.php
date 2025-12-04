<?php

namespace Flyokai\DataMate;

use Amp\Serialization\JsonSerializer as SerializerJson;
use Amp\Serialization\SerializationException;

function jsonSerializer(bool $objects = false): SerializerJson
{
    return $objects ? SerializerJson::withObjects(): SerializerJson::withAssociativeArrays();
}

function serializedArraySafeExtract(mixed $serialized, ?string $key = null): mixed
{
    $result = null;
    try {
        $data = $serialized;
        if (is_string($serialized)) {
            $jsonSerializer = jsonSerializer();
            $data = $jsonSerializer->unserialize($serialized);
        }
        if (is_array($data)) {
            $result = $key ? ($data[$key] ?? null) : $data;
        }
    } catch (SerializationException $exception) {
        $result = null;
    }
    return $result;
}

function fetchAltId(string|array $altIdKey, array|object $container): int|string|array|null
{
    static $fetchKey;
    $fetchKey ??= static function (string $key, array|object $container) {
        return is_object($container)
            ? ($container->{$key} ?? null)
            : ($container[$key] ?? null);
    };
    if (is_array($altIdKey)) {
        $isNull = true;
        $value = [];
        foreach ($altIdKey as $__k) {
            $value[$__k] = $fetchKey($__k, $container);
            $isNull = $isNull && $value[$__k] === null;
        }
        if ($isNull) {
            $value = null;
        }
    } else {
        $value = $fetchKey($altIdKey, $container);
    }
    return $value;
}

function normalizeAltIdKey(array|string $altIdKey): array|string
{
    if (is_array($altIdKey)) {
        sort($altIdKey);
    }
    return $altIdKey;
}

function prepareAltIdKeys(array $altIdKeys, string|array|object|null $container): array
{
    static $keyExist;
    $keyExist ??= static function (string $key, string|array|object|null $container) {
        return is_string($container) || is_object($container)
            ? property_exists($container, $key)
            : ($container === null || array_key_exists($key, $container));
    };
    foreach ($altIdKeys as &$__k) {
        if (is_array($__k)) {
            foreach ($__k as $__k2) {
                if (!is_string($__k2)) {
                    throw new \InvalidArgumentException(sprintf(
                        'Invalid alternative id-key definition: %s', var_export($__k, true)
                    ));
                } elseif (!$keyExist($__k2, $container)) {
                    throw new \InvalidArgumentException(sprintf(
                        'No matching DTO property for alternative id-key "%s"', $__k2
                    ));
                }
            }
            sort($__k);
        } elseif (!is_string($__k)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid alternative id-key definition: %s', var_export($__k, true)
            ));
        } elseif (!$keyExist($__k, $container)) {
            throw new \InvalidArgumentException(sprintf(
                'No matching DTO property for alternative id-key "%s"', $__k
            ));
        }
    }
    unset($__k);
    return array_values($altIdKeys);
}

function setObjectPrivateProperty($obj, $prop, $value, $class = null)
{
    return objectPrivatePropertyHelper()->set($obj, $prop, $value, $class);
}

function getObjectPrivateProperty($obj, $prop, $class = null)
{
    return objectPrivatePropertyHelper()->get($obj, $prop, $class);
}

function objectPrivatePropertyHelper()
{
    static $helper;
    return $helper ??= new Helper\ObjectPrivateProperty();
}

/**
 * @param string|object $class
 * @param bool $throw
 * @return class-string|false
 */
function className(string|object $class, bool $throw = true): string|false
{
    if (is_object($class)) {
        $class = get_class($class);
    }
    static $cache = [];
    if (isset($cache[$class])) {
        return $cache[$class];
    }
    // See https://www.php.net/manual/en/language.oop5.basic.php
    if (!\preg_match('(^\\\\?(?:[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*\\\\)*[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$)', $class)) {
        if ($throw) {
            throw new \Error('Invalid class name: ' . $class);
        } else {
            return false;
        }
    }
    $normalizedClass = \strtolower(\ltrim($class, '\\'));
    $cache[$class] = $normalizedClass;
    return $normalizedClass;
}

function isClassName(string $class): bool
{
    return (bool)className($class, false);
}
