<?php

namespace Flyokai\DataMate\Attribute;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class Json
{
    /**
     * @param class-string|null $target
     *     When set, the runtime helper decodes the JSON string and hands the decoded
     *     array to Valinor's dtoMapper() with this class as the target. Required when
     *     the parameter's declared type is an interface (e.g. GreyData).
     */
    public function __construct(
        public readonly ?string $target = null,
    ) {}
}
