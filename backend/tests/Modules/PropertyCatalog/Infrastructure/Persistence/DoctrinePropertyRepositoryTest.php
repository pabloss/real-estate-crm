<?php

declare(strict_types=1);

namespace App\Tests\Modules\PropertyCatalog\Infrastructure\Persistence;

use App\Modules\PropertyCatalog\Domain\Entity\Property;
use App\Modules\PropertyCatalog\Domain\Exception\PropertyNotFoundException;
use App\Modules\PropertyCatalog\Domain\ValueObject\PropertyArea;
use App\Modules\PropertyCatalog\Domain\ValueObject\PropertyPrice;
use App\Modules\PropertyCatalog\Infrastructure\Persistence\DoctrinePropertyRepository;
use App\Tests\SQLiteTestCase;
use Symfony\Component\Uid\Uuid;

final class DoctrinePropertyRepositoryTest extends SQLiteTestCase
{
    private DoctrinePropertyRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new DoctrinePropertyRepository(
            self::getContainer()->get('doctrine')
        );
    }

    public function test_it_saves_and_retrieves_property_from_sqlite_database(): void
    {
        // Arrange
        $propertyId = Uuid::v4();
        $property = new Property(
            $propertyId,
            'Modern Apartment in Center',
            new PropertyPrice(65000000), // 650,000.00
            new PropertyArea(65.5)
        );

        // Act
        $this->repository->save($property);

        // Clear Doctrine identity map to ensure it fetches from the database
        $this->entityManager->clear();

        $retrievedProperty = $this->repository->getById($propertyId);

        // Assert
        $this->assertInstanceOf(Property::class, $retrievedProperty);
        $this->assertEquals($propertyId->toRfc4122(), $retrievedProperty->getId()->toRfc4122());
        $this->assertEquals(0, $retrievedProperty->getInterestedLeadsCount());
    }

    public function test_it_throws_exception_when_property_not_found(): void
    {
        // Expect
        $this->expectException(PropertyNotFoundException::class);

        // Act
        $this->repository->getById(Uuid::v4());
    }
}
