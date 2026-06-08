<?php

namespace Flyokai\DataMate\Dto;

use Flyokai\DataMate\Helper\ArrayPath;
use Flyokai\DataMate\Helper\DtoTrait;

class ConfigJar
{
    use DtoTrait;
    use ArrayPath;

    public function __construct(
        public readonly array $options,
    )
    {
    }

    public function get(string $path, mixed $default = null): mixed
    {
        return $this->__fetchByPath($this->options, $path, $default);
    }

    public function set(string $path, mixed $value): static
    {
        $options = $this->__insertByPath($this->options, $path, $value);
        return $this->with(options: $options);
    }
}
