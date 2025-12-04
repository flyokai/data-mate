<?php

namespace Flyokai\DataMate\Helper;

/**
 * @template TItem
 */
trait JarTrait
{
    /**
     * @return TItem|null
     */
    public function get(string|int $id): mixed
    {
        return $this->byId[$id] ?? null;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }
}
