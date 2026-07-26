<?php

declare(strict_types=1);

namespace App\Modules\PropertyCatalog\Application\Query;

use App\Modules\PropertyCatalog\Application\DTO\PropertyView;
use Doctrine\DBAL\Connection;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')] // Ważne: przypisujemy do szyny zapytań
final readonly class GetPropertiesQueryHandler
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    /**
     * @return array<PropertyView>
     */
    public function __invoke(GetPropertiesQuery $query): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('id', 'title', 'price_amount_in_cents', 'area_square_meters', 'interested_leads_count', 'main_photo_url')
            ->from('properties')
            ->orderBy('title', 'ASC');

        $result = $qb->executeQuery()->fetchAllAssociative();

        // Mapujemy surowe wiersze SQL na płaskie DTO
        return array_map(static fn (array $row) => new PropertyView(
            $row['id'],
            $row['title'],
            $row['price_amount_in_cents'],
            (float) $row['area_square_meters'],
            (int) $row['interested_leads_count'],
            $row['main_photo_url'],
        ), $result);
    }
}
