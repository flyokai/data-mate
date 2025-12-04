<?php

namespace Flyokai\DataMate\Helper;

trait Solid
{
    public function toFragile(?\CuyZ\Valinor\Mapper\TreeMapper $mapper = null): \Flyokai\DataMate\Dto
    {
        return call_user_func([$this->fragileClassName, 'fromArray'], $this->toArray());
    }

}
