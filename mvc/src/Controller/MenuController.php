<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Menu;
use App\View\View;

// The controller: reads the request, asks the model, picks a view. That's all.
final class MenuController
{
    public function __construct(private Menu $menu, private View $view)
    {
    }

    public function list(array $query): Response
    {
        // Never trust input: a category is one of ours, or it's ignored.
        $category = $query['category'] ?? null;
        if (!in_array($category, $this->menu->categories(), true)) {
            $category = null;
        }

        return new Response(200, $this->view->render('list', [
            'categories' => $this->menu->categories(),
            'category' => $category,
            'items' => $this->menu->inCategory($category),
        ]));
    }

    public function show(array $query): Response
    {
        $id = filter_var($query['id'] ?? null, FILTER_VALIDATE_INT);
        $item = $id === false ? null : $this->menu->find($id);
        if ($item === null) {
            return new Response(404, $this->view->render('not-found', []));
        }

        return new Response(200, $this->view->render('item', ['item' => $item]));
    }
}
