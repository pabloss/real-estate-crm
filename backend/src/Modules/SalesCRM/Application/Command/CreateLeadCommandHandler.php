<?php

declare(strict_types=1);

namespace App\Modules\SalesCRM\Application\Command;

use App\Modules\SalesCRM\Domain\Entity\Lead;
use App\Modules\SalesCRM\Domain\ValueObject\Email;
use App\Modules\SalesCRM\Domain\Repository\LeadRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
final readonly class CreateLeadCommandHandler
{
    public function __construct(
        private LeadRepositoryInterface $repository
    ) {
    }

    public function __invoke(CreateLeadCommand $command): void
    {
        // Ręczne parsowanie UUID z opcjonalnego stringa
        $propertyId = $command->interestedInPropertyId
            ? Uuid::fromString($command->interestedInPropertyId)
            : null;

        $lead = new Lead(
            Uuid::v4(),
            $command->fullName,
            new Email($command->email),
            $command->phoneNumber,
            $propertyId
        );

        $this->repository->save($lead);
    }
}
