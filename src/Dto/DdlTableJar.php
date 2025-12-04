<?php

namespace Flyokai\DataMate\Dto;

use Flyokai\DataMate\Helper\DtoTrait;

class DdlTableJar
{
    use DtoTrait;

    private array $byName;

    /**
     * @param DdlTable[] $tables
     */
    public function __construct(
        public readonly array $tables,
    )
    {
        $this->initialize();
    }

    private function initialize(): void
    {
        foreach ($this->tables as $table) {
            $this->byName[$table->name] = $table;
        }
    }

    public function __wakeup(): void
    {
        $this->initialize();
    }

    public function table(string $name): ?DdlTable
    {
        return $this->byName[$name] ?? null;
    }
}
