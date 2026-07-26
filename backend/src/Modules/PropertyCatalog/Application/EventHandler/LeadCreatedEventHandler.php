<?php

declare(strict_types=1);

namespace App\Modules\PropertyCatalog\Application\EventHandler;

use App\Modules\PropertyCatalog\Domain\Repository\PropertyRepositoryInterface;
use App\Modules\SalesCRM\Domain\Event\LeadCreated;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler(bus: 'event.bus')]
final readonly class LeadCreatedEventHandler
{
    public function __construct(
        private PropertyRepositoryInterface $repository,
    ) {
    }

    public function __invoke(LeadCreated $event): void
    {
        if (null === $event->interestedInPropertyId) {
            // Zdarzenie nas nie interesuje, lead nie wskazał konkretnej oferty
            return;
        }

        try {
            $propertyId = Uuid::fromString($event->interestedInPropertyId);
            $property = $this->repository->getById($propertyId);

            // Zmiana stanu w Domenie
            $property->incrementInterestedLeads();

            // Zapis nowego stanu
            $this->repository->save($property);
        } catch (\Exception $e) {
            // Jeśli nieruchomość o danym UUID nie istnieje, cicho to ignorujemy.
            // Zdarzenia to "fakty dokonane" z przeszłości. Nie rzucamy wyjątkiem,
            // ponieważ nie chcemy wywrócić transakcji tworzenia Leada w SalesCRM!
        }
    }
}
