# flyokai/data-mate

> User docs → [`README.md`](README.md) · Agent quick-ref → [`CLAUDE.md`](CLAUDE.md) · Agent deep dive → [`AGENTS.md`](AGENTS.md)

> The DTO foundation of the Flyokai framework — Solid/Draft/GreyData states, identity, enums, and collections.

`data-mate` defines the contract every Data Transfer Object in Flyokai obeys. It pairs the safety of constructor-validated immutables (**Solid**) with the flexibility of dynamic schemas (**Draft**, **GreyData**), and it standardises identity (`HasId` / `HasAltId`), enum helpers, and item collections so the rest of the framework can treat data uniformly.

It is built on top of [`cuyz/valinor`](https://github.com/CuyZ/Valinor) for type-safe deserialization.

## Features

- **`Dto` interface** — `toArray()`, `toDbRow()`, `with()`, `cloneWith()`, `fromArgs()`
- **Three data states** — `Solid` (immutable, validated), `Draft` (flexible), `GreyData` (key/value bag)
- **Reflection-cached `DtoTrait`** — automatic Valinor mapping with undefined-vs-null tracking
- **Identity system** — `HasId`, `HasAltId`, `HasIdDto` with primary + alternative/composite keys
- **Enum helpers** — `StringEnumTrait`, `IntEnumTrait`, `LCStringEnumTrait`, `UCStringEnumTrait`
- **Collections** — `ItemJar` interface and `JarTrait` for type-safe iterables
- **Built-in DTOs** — `ConfigJar`, `DdlTable`, `DdlTableColumn`, `DdlTableJar`
- **Validation** — `Assertions`, `ValidationException`, `InvalidEntity` factories

## Installation

```bash
composer require flyokai/data-mate
```

## Quick start

### A Solid DTO (strict, immutable)

```php
use Flyokai\DataMate\Dto;
use Flyokai\DataMate\DtoTrait;
use Flyokai\DataMate\Solid;

final class UserSolid implements Dto, Solid
{
    use DtoTrait;

    public function __construct(
        public readonly int $userId,
        public readonly string $username,
        public readonly string $email,
        public readonly ?string $firstName = null,
    ) {}
}

$user = UserSolid::fromArray([
    'userId' => 1,
    'username' => 'alice',
    'email' => 'alice@example.com',
]);

$row    = $user->toDbRow();             // ['user_id' => 1, 'username' => 'alice', ...]
$copy   = $user->cloneWith(email: 'a@b.com');
$loose  = $user->with(email: 'a@b.com'); // Valinor-mapped, accepts loose types
```

### A Draft / Solid pairing

```php
use Flyokai\DataMate\Draft;
use Flyokai\DataMate\DtoTrait;

final class User implements Dto, Draft
{
    use DtoTrait;
    protected static string $solidClassName = UserSolid::class;
    // …mutable, flexible properties for input/staging
}
```

### Identity

```php
use Flyokai\DataMate\HasId;
use Flyokai\DataMate\HasAltId;
use Flyokai\DataMate\HasIdDto;

final class UserSolid implements HasIdDto, Solid
{
    use DtoTrait;

    protected static string $type      = 'user';
    protected static string $idKey     = 'user_id';
    protected static array  $altIdKeys = ['username', 'email'];

    public function __construct(
        public readonly int $userId,
        public readonly string $username,
        public readonly string $email,
    ) {}
}

$user->id();                   // 1
$user->idKey();                // 'user_id'
$user->altId('username');      // 'alice'
$user->altId(['username', 'email']);
$user->extractIdentity();      // 1, with fallback to alt IDs
```

## The data-state model

| Marker | Mutability | Construction | Use it for |
|--------|-----------|--------------|------------|
| `Solid` | Immutable, `public readonly` props | Constructor, validated | Domain objects, repositories return these |
| `Draft` | Mutable, flexible | Same DTO class as Solid but relaxed | Input staging, partial updates |
| `GreyData` | Dynamic key/value | Backed by `$data` array | API payloads, third-party blobs |

`fromArray()` uses Valinor for tolerant deserialisation; `cloneWith()` goes through the constructor for strict validation.

`toArray()` skips properties that were never assigned (undefined ≠ explicit null). `toDbRow()` builds on top of it and JSON-encodes any remaining array values — so `['data' => ['k' => 'v']]` becomes `['data' => '{"k":"v"}']`.

## GreyData

`GreyDataTrait` implements a path-aware key/value bag for objects whose schema is dynamic:

```php
$config->get('db/connection/default/host');     // path-based
$config->set('feature/flag/x', true);
$config->hasFeatureX();                         // magic getter (snake_case)
$config->withFeatureX(true);                    // immutable variant
```

## Enums

```php
use Flyokai\DataMate\StringEnumTrait;

enum Status: string
{
    use StringEnumTrait;

    case Active   = 'active';
    case Disabled = 'disabled';
}

Status::allowedValues();   // ['active', 'disabled']
Status::fromName('Active');
Status::tryFromName('xxx');
Status::validate('active');
Status::normalize('ACTIVE');
```

`LCStringEnumTrait` lower-cases input before lookup; `UCStringEnumTrait` upper-cases.

## Collections (ItemJar)

```php
use Flyokai\DataMate\ItemJar;
use Flyokai\DataMate\JarTrait;

final class UserJar implements ItemJar
{
    use JarTrait;
}

$jar = new UserJar();
$jar->add($user1);
$jar->get($user1->id());
foreach ($jar as $u) { /* … */ }
```

## API surface

| Class / Trait | Purpose |
|---------------|---------|
| `Dto` | Core interface |
| `DtoTrait` | Reflection-based Valinor implementation |
| `Solid` / `Draft` / `GreyData` | State markers |
| `HasId` / `HasAltId` / `HasIdDto` | Identity |
| `EnumTrait`, `StringEnumTrait`, `IntEnumTrait`, `LCStringEnumTrait`, `UCStringEnumTrait` | Enum helpers |
| `ItemJar`, `JarTrait` | Type-safe collections |
| `ConfigJar`, `DdlTable`, `DdlTableJar` | Built-in DTOs |
| `Assertions` | `assertNotNull`, `assertPositiveInt`, `assertPositiveFloat` |
| `ValidationException`, `InvalidEntity` | Errors |
| `dtoMapper(...)` | Cached configured Valinor `TreeMapper` |

## Gotchas

- `with()` is Valinor-flexible (accepts loose types, slower). `cloneWith()` is constructor-strict (faster).
- `toDbRow()` JSON-encodes arrays — be aware when reading rows back.
- A constructor parameter that was never supplied is "undefined" and is skipped in `toArray()`. An explicit `null` is included.
- `GreyData` does not track undefined — every key in `$data` is preserved.
- `__sleep()` only serializes constructor params — implement `__wakeup()` to reinitialize derived state.
- Reflection caches are global static and never cleared (intentional, for performance).
- Alt-ID keys are strings; composite keys are arrays of strings, normalised via sorting.

## See also

- [`flyokai/db-schema`](../db-schema/README.md) — `#[Table]` attribute on Solid DTOs becomes the schema source of truth
- [`flyokai/search-criteria`](../search-criteria/README.md) — declarative filters for repositories built on Solid DTOs
- [`flyokai/magento-dto`](../magento-dto/README.md) — DTOs for Magento entities

## License

MIT
