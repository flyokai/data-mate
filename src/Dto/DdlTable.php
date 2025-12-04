<?php

namespace Flyokai\DataMate\Dto;

use Flyokai\DataMate\Helper\DtoTrait;

class DdlTable
{
    use DtoTrait;

    private array $columnsByName;
    private array $columnsDefaults;

    /**
     * @param DdlTableColumn[] $columns
     */
    public function __construct(
        public readonly string $name,
        public readonly string $tableName,
        public readonly array  $columns,
    )
    {
        $this->initialize();
    }

    private function initialize(): void
    {
        foreach ($this->columns as $column) {
            $this->columnsByName[$column->name] = $column;
            $this->columnsDefaults[$column->name] = !$column->nullable ? $column->default : null;
        }
    }

    public function __wakeup(): void
    {
        $this->initialize();
    }

    public function column(string $columnName): ?DdlTableColumn
    {
        return $this->columnsByName[$columnName] ?? null;
    }

    public function columnNames(): array
    {
        return array_keys($this->columnsByName);
    }

    public function columnsDefaults(): array
    {
        return $this->columnsDefaults;
    }

    public function columnDefault(string $columnName): int|string|null
    {
        if ($this->columns[$columnName] ?? false) {
            $default = $this->columns[$columnName]['DEFAULT'] ?? null;
            $nullable = $this->columns[$columnName]['NULLABLE'] ?? true;
            return !$nullable ? $default : null;
        } else {
            throw new \RuntimeException(sprintf(
                'Table "%s" column "%s" does not exist.',
                $this->name, $columnName
            ));
        }
    }

    public function prepareRow(array $data): array
    {
        $row = [];
        foreach ($this->columns as $column) {
            if (!array_key_exists($column->name, $data)) {
                $row[$column->name] = $column->nullable ? null : $column->default;
            } else {
                $row[$column->name] = $data[$column->name];
            }
        }
        return $row;
    }
}
