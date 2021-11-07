<?php

declare(strict_types=1);

namespace App\Model;

trait EventsTrait
{
    private array $recordedEvents = [];

    protected function mergeEventsFrom(AggregateRoot $objectWithEvents): void
    {
        $this->recordedEvents = array_merge($this->recordedEvents, $objectWithEvents->releaseEvents());
    }

    protected function recordEvent(object $event): void
    {
        $this->recordedEvents[] = $event;
    }

    public function releaseEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];
        return $events;
    }
}
