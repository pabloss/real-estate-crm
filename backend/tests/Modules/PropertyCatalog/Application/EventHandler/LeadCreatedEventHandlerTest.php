<?php

declare(strict_types=1);

namespace App\Tests\Modules\PropertyCatalog\Application\EventHandler;

use App\Modules\PropertyCatalog\Application\EventHandler\LeadCreatedEventHandler;
use App\Modules\PropertyCatalog\Domain\Entity\Property;
use App\Modules\PropertyCatalog\Domain\Repository\PropertyRepositoryInterface;
use App\Modules\PropertyCatalog\Domain\ValueObject\PropertyArea;
use App\Modules\PropertyCatalog\Domain\ValueObject\PropertyPrice;
use App\Modules\SalesCRM\Domain\Event\LeadCreated;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class LeadCreatedEventHandlerTest extends TestCase
{
    public function testItIncrementsInterestedLeadsCountWhenPropertyExists(): void
    {
        // Arrange
        $propertyId = Uuid::v4();
        $event = new LeadCreated(
            (string) Uuid::v4(),
            $propertyId->toRfc4122(),
            new \DateTimeImmutable()
        );

        $property = clone $this->createDummyProperty($propertyId);

        $repositoryMock = $this->createMock(PropertyRepositoryInterface::class);
        $repositoryMock->expects($this->once())
            ->method('getById')
            ->with($propertyId)
            ->willReturn($property);

        // Oczekujemy, że metoda save() zostanie wywołana raz po zaktualizowaniu encji
        $repositoryMock->expects($this->once())
            ->method('save')
            ->with($property);

        $handler = new LeadCreatedEventHandler($repositoryMock);

        // Act
        $handler($event);

        // Assert - czy faktycznie podbiło licznik?
        // (Wymaga dodania publicznej metody getInterestedLeadsCount() do encji Property na potrzeby testów)
        $this->assertEquals(1, $property->getInterestedLeadsCount());
    }

    public function testItIgnoresEventIfLeadIsNotInterestedInSpecificProperty(): void
    {
        // Arrange
        $event = new LeadCreated((string) Uuid::v4(), null, new \DateTimeImmutable());

        $repositoryMock = $this->createMock(PropertyRepositoryInterface::class);
        // Oczekujemy, że repozytorium w ogóle nie zostanie dotknięte
        $repositoryMock->expects($this->never())->method('getById');
        $repositoryMock->expects($this->never())->method('save');

        $handler = new LeadCreatedEventHandler($repositoryMock);

        // Act
        $handler($event);
    }

    private function createDummyProperty(Uuid $id): Property
    {
        return new Property(
            $id,
            'Test Property',
            new PropertyPrice(50000000),
            new PropertyArea(50.0)
        );
    }
}
