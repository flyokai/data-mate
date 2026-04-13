<?php

namespace Flyokai\DataMate\Helper;

trait Draft
{
    public function toSolid(?\CuyZ\Valinor\Mapper\TreeMapper $mapper = null): \Flyokai\DataMate\Dto
    {
        $solid = call_user_func([$this->solidClassName, 'fromArray'], $this->toArray());
        if (!empty($this->extensions())) {
            $solid = $solid->withExtensions($this->extensions());
        }
        return $solid;
    }
}
