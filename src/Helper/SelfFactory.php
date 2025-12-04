<?php

namespace Flyokai\DataMate\Helper;

trait SelfFactory
{
    public static function selfFactory(...$args): static
    {
        return new static(...$args);
    }
}
