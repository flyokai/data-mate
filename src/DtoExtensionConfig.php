<?php

namespace Flyokai\DataMate;

/**
 * Configuration for a DTO extension: maps a base entity to its extension DTO pair and DB table.
 *
 * Registered via DI Composition in DtoExtensionRegistry.
 */
readonly class DtoExtensionConfig
{
    /**
     * @param class-string<Solid&Dto> $baseSolidClass   Base entity Solid DTO class
     * @param class-string<Draft&Dto> $baseDraftClass   Base entity Draft DTO class
     * @param class-string<Solid&DtoExtension> $solidClass Extension Solid DTO class
     * @param class-string<Draft&DtoExtension> $draftClass Extension Draft DTO class
     * @param string $tableName                         Extension DB table name
     * @param string $foreignKeyColumn                  FK column in extension table referencing base PK
     */
    public function __construct(
        public string $baseSolidClass,
        public string $baseDraftClass,
        public string $solidClass,
        public string $draftClass,
        public string $tableName,
        public string $foreignKeyColumn,
    ) {}
}
