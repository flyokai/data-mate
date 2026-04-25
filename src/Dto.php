<?php

namespace Flyokai\DataMate;

interface Dto
{
    /**
     * @return class-string
     */
    public function className(): string;

    /**
     * @param mixed ...$args
     * @return static
     */
    public function with(...$args): static;

    /**
     * @param mixed ...$args
     * @return static
     */
    public function cloneWith(...$args): static;

    /**
     * @return mixed[]
     */
    public function toArray(): array;

    /**
     * @return mixed[]
     */
    public function toDbRow(): array;

    /**
     * @param mixed ...$args
     * @return static
     */
    public static function fromArgs(...$args): static;
}
