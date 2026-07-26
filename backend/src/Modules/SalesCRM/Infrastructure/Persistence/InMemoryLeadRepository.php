<?php

declare(strict_types=1);

namespace App\Modules\SalesCRM\Infrastructure\Persistence;

use App\Modules\PropertyCatalog\Domain\Exception\PropertyNotFoundException;
use App\Modules\SalesCRM\Domain\Entity\Lead;
use App\Modules\SalesCRM\Domain\Enum\LeadStatus;
use App\Modules\SalesCRM\Domain\Repository\LeadRepositoryInterface;
use App\Modules\SalesCRM\Domain\ValueObject\Email;
use Symfony\Component\Uid\Uuid;

final class InMemoryLeadRepository implements LeadRepositoryInterface
{
    /**
     * @var array<string, Lead>
     */
    private array $leads = [];

    public function __construct()
    {
        $lead1 = new Lead(Uuid::v4(), 'Jan Kowalski', new Email('jan.kowalski@example.com'), '111222333');
        $this->leads[$lead1->getId()->toRfc4122()] = $lead1;

        $lead2 = new Lead(Uuid::v4(), 'Anna Nowak', new Email('anna.nowak@example.com'), '444555666');
        $lead2->markAsContacted();
        $this->leads[$lead2->getId()->toRfc4122()] = $lead2;

        $lead3 = new Lead(Uuid::v4(), 'Piotr Zając', new Email('piotr.zajac@example.com'), '777888999');
        $this->leads[$lead3->getId()->toRfc4122()] = $lead3;
    }

    public function save(Lead $lead): void
    {
        $this->leads[$lead->getId()->toRfc4122()] = $lead;
    }

    public function getById(Uuid $id): Lead
    {
        $lead = $this->leads[$id->toRfc4122()] ?? null;

        if (null === $lead) {
            throw PropertyNotFoundException::withMessage(sprintf('Lead with ID %s not found', $id->toRfc4122()));
        }

        return $lead;
    }

    public function countAll(): int
    {
        return count($this->leads);
    }

    public function countByStatus(): array
    {
        $counts = [];
        foreach (LeadStatus::cases() as $status) {
            $counts[$status->value] = 0;
        }

        foreach ($this->leads as $lead) {
            ++$counts[$lead->getStatus()->value];
        }

        return $counts;
    }
}
