<?php

declare(strict_types=1);

namespace App\Modules\SalesCRM\Infrastructure\Persistence;

use App\Modules\PropertyCatalog\Domain\Exception\PropertyNotFoundException;
use App\Modules\SalesCRM\Domain\Entity\Lead;
use App\Modules\SalesCRM\Domain\Repository\LeadRepositoryInterface;
use Symfony\Component\Uid\Uuid;

final class InMemoryLeadRepository implements LeadRepositoryInterface
{
    /**
     * @var array<string, Lead>
     */
    private array $leads = [];

    public function save(Lead $lead): void
    {
        $this->leads[$lead->getId()->toRfc4122()] = $lead;
    }

    public function getById(Uuid $id): Lead
    {
        $lead = $this->leads[$id->toRfc4122()] ?? null;

        if ($lead === null) {
            throw PropertyNotFoundException::withMessage(sprintf('Lead with ID %s not found', $id->toRfc4122()));
        }

        return $lead;
    }
}
