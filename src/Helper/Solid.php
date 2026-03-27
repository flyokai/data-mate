<?php

namespace Flyokai\DataMate\Helper;

trait Solid
{
    public function toDraft(?\CuyZ\Valinor\Mapper\TreeMapper $mapper = null): \Flyokai\DataMate\Dto
    {
        return call_user_func([$this->draftClassName, 'fromArray'], $this->toArray());
    }

}
