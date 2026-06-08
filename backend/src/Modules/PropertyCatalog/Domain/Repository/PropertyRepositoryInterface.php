<?php

declare(strict_types=1);

namespace App\Modules\PropertyCatalog\Domain\Repository;

use App\Modules\PropertyCatalog\Domain\Entity\Property;
use App\Modules\PropertyCatalog\Domain\Exception\PropertyNotFoundException;
use Symfony\Component\Uid\Uuid;

interface PropertyRepositoryInterface
{
    public function save(Property $property): void;

    /**
     * @throws PropertyNotFoundException
     */
    public function getById(Uuid $id): Property;
}
