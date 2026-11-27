<?php

declare(strict_types=1);

namespace App\View;

// Runs a template from views/ and hands back what it printed.
final class View
{
    public function __construct(private string $folder)
    {
    }

    public function render(string $template, array $view): string
    {
        ob_start();
        require $this->folder . '/' . $template . '.php';
        return ob_get_clean();
    }
}
