<?php

declare(strict_types=1);

namespace App\Modules\PropertyCatalog\Infrastructure\Persistence;

use App\Modules\PropertyCatalog\Domain\Entity\Property;
use App\Modules\PropertyCatalog\Domain\Exception\PropertyNotFoundException;
use App\Modules\PropertyCatalog\Domain\Repository\PropertyRepositoryInterface;
use Symfony\Component\Uid\Uuid;

final class InMemoryPropertyRepository implements PropertyRepositoryInterface
{
    /**
     * @var array<string, Property>
     */
    private array $properties = [];

    public function save(Property $property): void
    {
        $this->properties[$property->getId()->toRfc4122()] = $property;
    }

    public function getById(Uuid $id): Property
    {
        $property = $this->properties[$id->toRfc4122()] ?? null;

        if ($property === null) {
            throw PropertyNotFoundException::withMessage(sprintf('Property with ID %s not found', $id->toRfc4122()));
        }

        return $property;
    }
}
