<?php

declare(strict_types=1);

namespace App\Modules\PropertyCatalog\Infrastructure\Persistence;

use App\Modules\PropertyCatalog\Domain\Entity\Property;
use App\Modules\PropertyCatalog\Domain\Exception\PropertyNotFoundException;
use App\Modules\PropertyCatalog\Domain\Repository\PropertyRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

final class DoctrinePropertyRepository extends ServiceEntityRepository implements PropertyRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    public function save(Property $property): void
    {
        $this->getEntityManager()->persist($property);
        $this->getEntityManager()->flush(); // W czystym CQRS flush można przenieść do middleware na szynie
    }

    public function getById(Uuid $id): Property
    {
        $property = $this->find($id);

        if (!$property instanceof Property) {
            throw PropertyNotFoundException::withMessage('Property not found');
        }

        return $property;
    }
}
