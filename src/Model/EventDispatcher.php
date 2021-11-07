<?php
declare(strict_types=1);

namespace App\Model;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventDispatcher //https://romaricdrigon.github.io/2019/08/09/domain-events
{
    private EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function dispatch(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatcher->dispatch($event);
        }
    }
}
