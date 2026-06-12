<?php

declare(strict_types=1);

namespace App\Modules\SalesCRM\Application\Query;

use App\Modules\SalesCRM\Application\DTO\LeadView;
use App\Modules\SalesCRM\Domain\Entity\Lead;
use Doctrine\DBAL\Connection;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')] // Ważne: przypisujemy do szyny zapytań

final readonly class GetLeadsQueryHandler
{
    public function __construct(
        private Connection $connection
    )
    {
    }

    public function __invoke(GetLeadsQuery $query): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select(
            'id',
            'full_name',
            'phone_number',
            'status',
            'interested_in_property_id',
            'email',
        )
            ->from('leads')
            ->orderBy('full_name', 'ASC');

        $result = $qb->executeQuery()->fetchAllAssociative();

        return array_map(static fn (array $row) => new LeadView(
            $row['id'],
            $row['full_name'],
            $row['phone_number'],
            $row['status'],
            $row['interested_in_property_id'] ?? null,
            $row['email']
        ), $result);
    }
}
