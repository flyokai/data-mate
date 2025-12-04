<?php

namespace Flyokai\DataMate;

use CuyZ\Valinor\Mapper\TreeMapper;

interface Solid
{
    public function toFragile(?TreeMapper $mapper = null): Dto;
}
