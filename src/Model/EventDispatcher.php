<?php
declare(strict_types=1);

namespace App\Model;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final readonly class EventDispatcher //https://romaricdrigon.github.io/2019/08/09/domain-events
{
    public function __construct(private EventDispatcherInterface $dispatcher) {}

    public function dispatch(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatcher->dispatch($event);
        }
    }
}
