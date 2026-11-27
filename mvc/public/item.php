<?php

declare(strict_types=1);

use App\Controller\MenuController;
use App\Model\Menu;
use App\View\View;

require dirname(__DIR__) . '/vendor/autoload.php';

// Build the pieces, hand the request to the controller, send what it returns.
$menu = new Menu(require dirname(__DIR__) . '/data/menu.php');
$controller = new MenuController($menu, new View(dirname(__DIR__) . '/views'));

$response = $controller->show($_GET);
http_response_code($response->status);
echo $response->body;
