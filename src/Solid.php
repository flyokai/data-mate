<?php

namespace Flyokai\DataMate;

use CuyZ\Valinor\Mapper\TreeMapper;

interface Solid
{
    public function toDraft(?TreeMapper $mapper = null): Dto;
}
