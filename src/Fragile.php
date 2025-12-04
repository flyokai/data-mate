<?php

namespace Flyokai\DataMate;

use CuyZ\Valinor\Mapper\TreeMapper;

interface Fragile
{
    public function toSolid(?TreeMapper $mapper = null): Dto;
}
