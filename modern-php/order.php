<?php
declare(strict_types=1);

// An enum: a type with a fixed list of values. This one is backed by strings,
// so each case has a value you can store: in a VARCHAR column, never a MySQL ENUM.
enum Status: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Shipped = 'shipped';

    // match: like switch, but it returns a value, compares with ===, and has no fall-through.
    public function label(): string
    {
        return match ($this) {
            Status::Pending => 'waiting for payment',
            Status::Paid => 'paid, not shipped yet',
            Status::Shipped => 'on its way',
        };
    }
}

// readonly: every property is set once, in the constructor, and never changed.
// Constructor promotion: the parameters are the properties, declared in one place.
final readonly class Coupon
{
    public function __construct(public string $code, public int $percentOff)
    {
    }
}

final readonly class Order
{
    public function __construct(
        public int $id,
        public Status $status,
        public int $totalCents,
        public ?Coupon $coupon = null,
    ) {
    }
}

// Never trust input: tryFrom() gives null for anything that isn't a real status.
$status = Status::tryFrom($argv[1] ?? '');
if ($status === null) {
    echo 'Unknown status. Use one of: ', implode(', ', array_map(fn (Status $s) => $s->value, Status::cases())), "\n";
    exit(1);
}

// Named arguments: each value says which parameter it's for.
$plain = new Order(id: 1042, status: $status, totalCents: 4_500);
$discounted = new Order(id: 1043, status: $status, totalCents: 4_500, coupon: new Coupon('FALL10', 10));

foreach ([$plain, $discounted] as $order) {
    // The nullsafe operator: ?-> gives null, instead of an error, when there's no coupon.
    $code = $order->coupon?->code ?? 'none';
    echo "Order {$order->id}: {$order->status->label()}, coupon: {$code}\n";
}

// Stored as a plain string, ready for the database.
echo 'Saved as: ', $plain->status->value, "\n";

try {
    $plain->totalCents = 1;
} catch (Error $e) {
    echo $e->getMessage(), "\n";
}
