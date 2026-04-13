<?php

namespace Flyokai\DataMate\Helper;

trait Solid
{
    public function toDraft(?\CuyZ\Valinor\Mapper\TreeMapper $mapper = null): \Flyokai\DataMate\Dto
    {
        $draft = call_user_func([$this->draftClassName, 'fromArray'], $this->toArray());
        if (!empty($this->extensions())) {
            $draft = $draft->withExtensions($this->extensions());
        }
        return $draft;
    }

}
