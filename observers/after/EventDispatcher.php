<?php

declare(strict_types=1);

// Keeps a list of who wants to hear about each kind of event, and tells them.
final class EventDispatcher
{
    /** @var array<string, callable[]> */
    private array $listeners = [];

    public function listen(string $eventClass, callable $listener): void
    {
        $this->listeners[$eventClass][] = $listener;
    }

    public function dispatch(object $event): void
    {
        foreach ($this->listeners[$event::class] ?? [] as $listener) {
            $listener($event);
        }
    }
}
