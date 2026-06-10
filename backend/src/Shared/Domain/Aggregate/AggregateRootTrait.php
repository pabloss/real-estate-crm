<?php

declare(strict_types=1);

namespace App\Shared\Domain\Aggregate;

use App\Shared\Domain\Event\DomainEventInterface;

trait AggregateRootTrait
{
    /** @var DomainEventInterface[] */
    private array $domainEvents = [];

    protected function record(DomainEventInterface $event): void
    {
        $this->domainEvents[] = $event;
    }

    /**
     * @return DomainEventInterface[]
     */
    public function releaseEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }
}
