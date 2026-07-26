<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Application\Query;

use App\Modules\PropertyCatalog\Domain\Repository\PropertyRepositoryInterface;
use App\Modules\SalesCRM\Domain\Enum\LeadStatus;
use App\Modules\SalesCRM\Domain\Repository\LeadRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')] // Ważne: przypisujemy do szyny zapytań
final readonly class GetDashboardStatsQueryHandler
{
    public function __construct(
        private PropertyRepositoryInterface $propertyRepository,
        private LeadRepositoryInterface $leadRepository,
    ) {}

    /**
     * @return array{totalProperties: int, totalLeads: int, leadsByStatus: array<string, int>}
     */
    public function __invoke(GetDashboardStatsQuery $query): array
    {
        $propertyCount = $this->propertyRepository->countAll();
        $leadCount = $this->leadRepository->countAll();
        $leadsByStatus = $this->leadRepository->countByStatus();

        // Uzupełniamy brakujące statusy, aby na froncie zawsze były te same klucze
        $allStatuses = array_map(fn ($case) => $case->value, LeadStatus::cases());
        $fullLeadsByStatus = [];
        foreach ($allStatuses as $status) {
            $fullLeadsByStatus[$status] = $leadsByStatus[$status] ?? 0;
        }

        return [
            'totalProperties' => $propertyCount,
            'totalLeads' => $leadCount,
            'leadsByStatus' => $fullLeadsByStatus,
        ];
    }
}
