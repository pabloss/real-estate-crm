<?php

declare(strict_types=1);

namespace App\Modules\SalesCRM\Infrastructure\Persistence;

use App\Modules\PropertyCatalog\Domain\Exception\PropertyNotFoundException;
use App\Modules\SalesCRM\Domain\Entity\Lead;
use App\Modules\SalesCRM\Domain\Repository\LeadRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

class DoctrineLeadRepository extends ServiceEntityRepository implements LeadRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lead::class);
    }

    public function save(Lead $lead): void
    {
        $this->getEntityManager()->persist($lead);
        $this->getEntityManager()->flush(); // W czystym CQRS flush można przenieść do middleware na szynie    }
    }

    /**
     * @inheritDoc
     */
    public function getById(Uuid $id): Lead
    {
        $lead = $this->find($id);

        if (!$lead instanceof Lead) {
            throw PropertyNotFoundException::withMessage('Lead not found');
        }

        return $lead;
    }
}
