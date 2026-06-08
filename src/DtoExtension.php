<?php

namespace Flyokai\DataMate;

/**
 * Marker interface for extension DTOs that extend a base entity via a 1:1 FK relation.
 *
 * Extension DTOs implement this interface along with either Solid or Draft
 * (just like any regular DTO pair). The base entity's repository decorator
 * uses DtoExtensionConfig (registered via DI) to discover and load/save extensions.
 */
interface DtoExtension extends Dto
{
    /**
     * Returns the base Solid DTO class this extension targets.
     *
     * @return class-string<Solid&Dto>
     */
    public static function baseClass(): string;

    /**
     * Returns the FK column name in the extension table that references the base entity's PK.
     */
    public static function foreignKeyColumn(): string;
}
