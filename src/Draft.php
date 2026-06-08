<?php

namespace Flyokai\DataMate;

use CuyZ\Valinor\Mapper\TreeMapper;

interface Draft
{
    public function toSolid(?TreeMapper $mapper = null): Dto;
}
