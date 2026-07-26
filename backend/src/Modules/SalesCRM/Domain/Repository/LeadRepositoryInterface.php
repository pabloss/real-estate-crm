<?php

declare(strict_types=1);

namespace App\Modules\SalesCRM\Domain\Repository;

use App\Modules\PropertyCatalog\Domain\Exception\PropertyNotFoundException;
use App\Modules\SalesCRM\Domain\Entity\Lead;
use Symfony\Component\Uid\Uuid;

interface LeadRepositoryInterface
{
    public function save(Lead $lead): void;

    /**
     * @throws PropertyNotFoundException
     */
    public function getById(Uuid $id): Lead;

    public function countAll(): int;

    public function countByStatus(): array;
}
