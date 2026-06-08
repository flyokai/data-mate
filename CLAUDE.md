# flyokai/data-mate

> User docs → [`README.md`](README.md) · Agent quick-ref → [`CLAUDE.md`](CLAUDE.md) · Agent deep dive → [`AGENTS.md`](AGENTS.md)

Base DTO interface, data abstractions (Solid/Draft/GreyData), identity system (HasId/HasAltId), enum support, and collection patterns for the Flyokai ecosystem.

See [AGENTS.md](AGENTS.md) for detailed module knowledge.

## Quick Reference

- **Core interface**: `Dto` with `toArray()`, `toDbRow()`, `cloneWith()`, `with()`, `fromArgs()`
- **Implementation trait**: `DtoTrait` (reflection-based, Valinor-powered)
- **Identity**: `HasId` (single), `HasAltId` (composite), `HasIdDto` (combined)
- **Data states**: `Solid` (strict/immutable) ↔ `Draft`/`GreyData` (dynamic/flexible)
- **Construction**: `fromArray()` (Valinor, flexible) vs `cloneWith()` (constructor, strict)
- **Enums**: `StringEnumTrait`, `IntEnumTrait`, `LCStringEnumTrait`, `UCStringEnumTrait`
- **Collections**: `ItemJar` interface + `JarTrait`
