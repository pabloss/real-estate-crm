<?php

declare(strict_types=1);

namespace App\Modules\SalesCRM\Domain\Event;

use App\Shared\Domain\Event\DomainEventInterface;

final readonly class LeadCreated implements DomainEventInterface
{
    public function __construct(
        public string $leadId,
        public ?string $interestedInPropertyId,
        public \DateTimeImmutable $occurredOn,
    ) {
    }
}
