<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Infrastructure\Controller;

use App\Modules\Dashboard\Application\Query\GetDashboardStatsQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/dashboard', name: 'api_dashboard_')]
final class DashboardController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $queryBus)
    {
        $this->messageBus = $queryBus;
    }

    #[Route('', name: 'stats', methods: ['GET'])]
    public function stats(): JsonResponse
    {
        $query = new GetDashboardStatsQuery();
        $stats = $this->handle($query);

        return $this->json($stats);
    }
}
