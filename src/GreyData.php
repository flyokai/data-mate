<?php

namespace Flyokai\DataMate;

interface GreyData extends HasId, HasAltId, Dto
{
    public function get(int|string $key): mixed;

    public function getId(): int|string|null;

    public function data(): array;

    public function set(string $key, mixed $value): static;

    public function __call($method, $args);
}
