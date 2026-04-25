# flyokai/data-mate

> User docs → [`README.md`](README.md) · Agent quick-ref → [`CLAUDE.md`](CLAUDE.md) · Agent deep dive → [`AGENTS.md`](AGENTS.md)

Base DTO interface, data abstractions, and helper traits for the entire Flyokai ecosystem.

## Key Abstractions

### Dto Interface
`Dto` — core contract for all Data Transfer Objects:
- `className(): string` — returns class-string
- `with(...$args): static` — create copy with merged args via Valinor mapping (flexible, slower)
- `cloneWith(...$args): static` — create copy via constructor (strict, faster)
- `toArray(): array` — serialize to associative array (nested objects call toArray recursively, BackedEnum unwraps to ->value)
- `toDbRow(): array` — extends toArray by JSON-encoding remaining array values
- `fromArgs(...$args): static` — factory from args (alias for fromArray)

### DtoTrait
Primary implementation trait for `Dto`. Provides:
- Reflection-based constructor introspection (cached in static arrays)
- `fromArray(array)` — uses Valinor `dtoMapper(allowSuperfluousKeys: true)` for flexible construction
- **Undefined tracking**: distinguishes "not provided" (undefined) vs "explicitly null". `toArray()` skips undefined properties.
- `tuneDbRow(array)` — override point for custom DB row processing
- `__sleep()` only serializes constructor params — private derived state lost; implement `__wakeup()` to reinitialize

### Data State Markers

| Interface | Purpose | Conversion Method |
|-----------|---------|-------------------|
| `Solid` | Type-safe, immutable, constructor-validated | `toDraft(?TreeMapper): Dto` |
| `Draft` | Dynamic, flexible, runtime-validated | `toSolid(?TreeMapper): Dto` |
| `GreyData` | Dynamic key-value container (extends HasId, HasAltId, Dto) | get/set/data/has methods |

**Solid**: Use for strict typing. Constructor-validated. Properties should be `public readonly`.
**Draft/GreyData**: Use for API responses, third-party data, flexible schema. Backed by `$data` array.

### GreyDataTrait
Implements `GreyData` interface:
- `get(key)`, `set(key, value)`, `has(key)`, `data()` for dynamic access
- Path-based access via `ArrayPath` trait: `fetchByPath('user/profile/name')`
- Magic methods: `hasX()`, `getX()`, `withX(val)`, `setX(val)` — X converted to snake_case
- Set behavior: if `data` property is readonly → clones; if mutable → mutates in-place
- Does NOT track undefined (all keys in $data preserved)

### Identity System

**HasId** — single primary identity:
- `id(): int|string|null`, `idKey(): string`, `entityType(): string`
- Implementation via static properties: `$type`, `$idKey`

**HasAltId** — alternative/composite identity:
- `altId(string|array $key)` — single key returns scalar, array key returns associative array
- `altIdKeys(): array` — all recognized alt IDs
- `extractIdentity()` — returns primary ID, falls back to alt IDs

**HasIdDto** — combines HasId + Dto

### Enum Support
- `EnumTrait` — base: `allowedValues()`, `allowedNames()`, `fromName()`, `tryFromName()`
- `StringEnumTrait` — string-backed: `validate()`, `fromValue()`, `normalize()`
- `IntEnumTrait` — int-backed
- `LCStringEnumTrait` — lowercase normalization
- `UCStringEnumTrait` — uppercase normalization

### Collections
- `ItemJar` interface (extends IteratorAggregate) — marker for type-safe collections
- `JarTrait` — provides `$byId[]`, `$items[]`, `get(id)`, `count()`, `getIterator()`

### Built-in DTOs
- `ConfigJar` — configuration container with path-based access (`get('path')`, `set('path', value)`)
- `DdlTable` / `DdlTableColumn` / `DdlTableJar` — database DDL definitions with column metadata and `prepareRow()` for defaults

### Valinor Integration
- `dtoMapper(...)` — cached configured TreeMapper with options: enableFlexibleCasting, allowPermissiveTypes, allowSuperfluousKeys, allowUndefinedValues
- `extractDtoMappingErrors(MappingError)` — parses Valinor errors into readable array

### Validation & Errors
- `Assertions` — static helpers: `assertNotNull()`, `assertPositiveInt()`, `assertPositiveFloat()`
- `ValidationException` — extends InvalidArgumentException
- `InvalidEntity` — factories: `missingId()`, `altIdKeyNotSupported()`, `ambiguous()`, etc.

## Conventions

1. **Constructor properties**: Must be `public readonly` or promoted — DtoTrait inspects only constructor params
2. **Identity setup**: Define `protected static string $type`, `$idKey`, `array $altIdKeys`
3. **Draft/Solid pairing**: Solid DTO has `use Solid` trait + `$draftClassName`; Draft has `use Draft` trait + `$solidClassName`
4. **Enum values**: Use lowercase for string enums when normalizing

## Gotchas

- **with() vs cloneWith()**: `with()` uses Valinor (flexible type casting, slower); `cloneWith()` uses constructor (strict, faster)
- **toDbRow() JSON-encodes arrays**: `['data' => ['k'=>'v']]` becomes `['data' => '{"k":"v"}']`
- **Undefined vs null**: Constructor param missing + nullable → marked undefined, skipped in toArray(). Explicit null → included.
- **GreyData doesn't track undefined**: All keys in `$data` preserved in toArray()
- **__sleep() drops derived state**: Only constructor params serialized. Implement `__wakeup()` for reinitialization.
- **Reflection caching**: Global static, never cleared — intentional for performance
- **Alt ID keys must be strings**: Single key or array of strings (composite sorted during normalization)
