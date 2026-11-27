<?php
declare(strict_types=1);

// __toString: PHP calls it whenever the object is used as a string.
final class Money
{
    public function __construct(private int $cents)
    {
    }

    public function __toString(): string
    {
        return sprintf('$%d.%02d', intdiv($this->cents, 100), $this->cents % 100);
    }
}

// __get and __isset: PHP calls them for a property the object doesn't have.
// A database row, without declaring a property for every column.
final class Row
{
    public function __construct(private array $columns)
    {
    }

    public function __get(string $name): mixed
    {
        return $this->columns[$name] ?? null; // Anything it doesn't have is null. Typos too.
    }

    public function __isset(string $name): bool
    {
        return isset($this->columns[$name]);
    }
}

// The same row with real properties, for comparison.
final class User
{
    public function __construct(public string $email, public string $name)
    {
    }
}

// __invoke: the object can be called like a function.
final class Greeter
{
    public function __invoke(string $name): string
    {
        return "Hello, {$name}.";
    }
}

// __debugInfo: what var_dump() and print_r() show.
final class ApiClient
{
    public function __construct(private string $baseUrl, #[\SensitiveParameter] private string $apiKey)
    {
    }

    public function __debugInfo(): array
    {
        return ['baseUrl' => $this->baseUrl, 'apiKey' => '(hidden)'];
    }
}

$price = new Money(1999);
echo "Price: {$price}\n";

$row = new Row(['email' => 'ada@example.com', 'name' => 'Ada']);
echo "Row email: {$row->email}\n";
echo "Row typo: [{$row->emial}]\n";

$user = new User('ada@example.com', 'Ada');
echo "User typo: [{$user->emial}]\n";

$greet = new Greeter();
echo $greet('Grace'), "\n";
echo implode(' ', array_map($greet, ['Ada', 'Katherine'])), "\n";

print_r(new ApiClient('https://api.example.com', 'not-a-real-key'));
