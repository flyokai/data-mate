<?php

namespace Flyokai\DataMate\Helper;

use Flyokai\DataMate\InvalidEntity;

trait GreyDataTrait
{
    use DtoTrait;
    use ArrayPath;
    public function has(int|string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function getData(int|string $key): mixed
    {
        return $this->get($key);
    }

    public function get(int|string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function id(): int|string|null
    {
        return $this->data[static::idKey()] ?? null;
    }

    public function getId(): int|string|null
    {
        return $this->id();
    }

    public static function entityType(): string
    {
        return static::$type;
    }

    public static function idKey(): string
    {
        return static::$idKey;
    }

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
        return \Flyokai\DataMate\fetchAltId($altIdKey, $this->data);
    }

    protected static function prepareAltIdKeys(array $altIdKeys): array
    {
        return \Flyokai\DataMate\prepareAltIdKeys($altIdKeys, null);
    }

    public function extractIdentity(): int|string|array|null
    {
        if ($this->id()) {
            return $this->id();
        }
        $entityId = [];
        foreach (static::altIdKeys() as $altIdKey) {
            if (!($__altId = $this->altId($altIdKey))) continue;
            if (!is_array($__altId)) {
                $__altId = [$altIdKey => $__altId];
            }
            $entityId = array_merge($entityId, $__altId);
        }
        return !empty($entityId) ? $entityId : null;
    }

    protected static array $__nameCache = [];

    protected function __underscore(string $name, int $prefixLength): string
    {
        if (isset(self::$__nameCache[$name])) {
            return self::$__nameCache[$name];
        }

        $result = strtolower(
            trim(
                preg_replace(
                    '/([A-Z]|[0-9]+)/',
                    "_$1",
                    lcfirst(
                        substr(
                            $name,
                            $prefixLength
                        )
                    )
                ),
                '_'
            )
        );

        self::$__nameCache[$name] = $result;
        return $result;
    }

    public function fetchByPath(string $path): mixed
    {
        return $this->__fetchByPath($this->data, $path);
    }

    public function withPathValue(string $path, mixed $value): static
    {
        $data = $this->__insertByPath($this->data, $path, $value);
        return static::cloneWith(
            data: $data
        );
    }

    public function __call($method, $args)
    {
        if (str_starts_with($method, 'has')) {
            return $this->has($this->__underscore($method, 3));
        }
        if (str_starts_with($method, 'get')) {
            return $this->get($this->__underscore($method, 3));
        }
        if (str_starts_with($method, 'with')) {
            $data = $this->data;
            $data[$this->__underscore($method, 4)] = $args[0] ?? null;
            return static::cloneWith(
                data: $data
            );
        }
        if (str_starts_with($method, 'set')) {
            return $this->set($this->__underscore($method, 3), $args[0] ?? null);
        }
        throw new \BadMethodCallException('Invalid method: ' . __METHOD__);
    }

    public function addData(array $data): static
    {
        $data = array_merge($this->data, $data);
        return $this->set($data, null);
    }

    public function setData(string|array $key, mixed $value = null): static
    {
        return $this->set($key, $value);
    }

    public function set(string|array $key, mixed $value): static
    {
        $clone = $this;
        if (static::reflector(static::class)->getProperty('data')->isReadOnly()) {
            if (is_array($key)) {
                $data = $key;
            } else {
                $data = $this->data;
                $data[$key] = $value;
            }
            $clone = static::cloneWith(
                data: $data
            );
        } else {
            if (is_array($key)) {
                $clone->data = $key;
            } else {
                $clone->data[$key] = $value;
            }
        }
        return $clone;
    }
}
