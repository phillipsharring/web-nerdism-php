<?php
declare(strict_types=1);

// What Composer's autoloader does, in a few lines. When PHP meets a class
// it hasn't loaded, it calls this function with the class's full name.
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return; // Not one of ours. Another autoloader may know it.
    }
    // App\Text\Shout becomes src/Text/Shout.php
    $file = __DIR__ . '/src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    echo "(loading {$file})\n";
    if (is_file($file)) {
        require $file;
    }
});

$greeter = new App\Greeter(new App\Text\Shout());
echo $greeter->greet('Ada');
