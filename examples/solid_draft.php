<?php
/**
 * data-mate example — round-trip Solid ↔ Draft, identity, and serialisation.
 *
 * Run from the project root:
 *   php vendor/flyokai/data-mate/examples/solid_draft.php
 */

require __DIR__ . '/../../../../vendor/autoload.php';

use Flyokai\DataMate\Dto;
use Flyokai\DataMate\DtoTrait;
use Flyokai\DataMate\Draft;
use Flyokai\DataMate\HasIdDto;
use Flyokai\DataMate\Solid;

// ---------- Solid: immutable, constructor-validated ----------

#[\AllowDynamicProperties]
final class WidgetSolid implements Dto, Solid, HasIdDto
{
    use DtoTrait;

    protected static string $type      = 'widget';
    protected static string $idKey     = 'widget_id';
    protected static array  $altIdKeys = ['sku'];

    public function __construct(
        public readonly int $widgetId,
        public readonly string $sku,
        public readonly string $name,
        public readonly float $price,
        public readonly ?string $description = null,
    ) {}
}

// Tolerant construction via Valinor:
$w1 = WidgetSolid::fromArray([
    'widgetId' => '1',                  // string → int OK
    'sku'      => 'SKU-1',
    'name'     => 'Foo',
    'price'    => 9.99,
]);

echo "Solid built: {$w1->name} (\${$w1->price}) widget_id={$w1->widgetId}\n";
echo "Identity: id={$w1->id()}, idKey={$w1->idKey()}, type={$w1->entityType()}\n";
echo "altId('sku') = {$w1->altId('sku')}\n";

// Strict copy — constructor-validated:
$w2 = $w1->cloneWith(price: 14.99);
echo "Cloned with price=14.99: {$w2->price}\n";

// Tolerant copy — Valinor will coerce:
$w3 = $w1->with(price: '24.99');
echo "with('24.99' string → float): {$w3->price}\n";

// Serialise:
print_r($w1->toArray());
print_r($w1->toDbRow());

// ---------- Draft: mutable, flexible ----------

final class Widget implements Dto, Draft
{
    use DtoTrait;
    protected static string $solidClassName = WidgetSolid::class;

    public function __construct(
        public ?int $widgetId = null,
        public ?string $sku = null,
        public ?string $name = null,
        public ?float $price = null,
        public ?string $description = null,
    ) {}
}

$draft = new Widget(sku: 'SKU-2', name: 'Bar', price: 4.99);
echo "Draft sku={$draft->sku}, no widget_id yet\n";

// You'd typically convert to Solid before persisting:
$asSolid = WidgetSolid::fromArray($draft->toArray() + ['widgetId' => 99]);
echo "Draft → Solid: widget_id={$asSolid->widgetId}\n";
