<?php

namespace Flyokai\DataMate\Dto;

use Flyokai\DataMate\Helper\DtoTrait;

class DdlTableColumn
{
    use DtoTrait;

    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly ?string $default,
        public readonly bool $nullable,
    )
    {
    }
}
