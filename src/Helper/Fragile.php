<?php

namespace Flyokai\DataMate\Helper;

trait Fragile
{
    public function toSolid(?\CuyZ\Valinor\Mapper\TreeMapper $mapper = null): \Flyokai\DataMate\Dto
    {
        return call_user_func([$this->solidClassName, 'fromArray'], $this->toArray());
    }
}
