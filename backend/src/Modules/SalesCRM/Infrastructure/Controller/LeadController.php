<?php

declare(strict_types=1);

namespace App\Modules\SalesCRM\Infrastructure\Controller;

use App\Modules\SalesCRM\Application\Command\CreateLeadCommand;
use App\Modules\SalesCRM\Application\Query\GetLeadsQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/leads', name: 'api_leads_')]
final class LeadController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        private readonly MessageBusInterface $commandBus,
        MessageBusInterface $queryBus,
    ) {
        $this->messageBus = $queryBus;
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        // Tutaj wywołujemy zapytanie DBAL, które zwraca tablicę DTO LeadView
        $leads = $this->handle(new GetLeadsQuery());

        return $this->json($leads);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = $request->toArray();

        $command = new CreateLeadCommand(
            fullName: $payload['fullName'] ?? '',
            email: $payload['email'] ?? '',
            phoneNumber: $payload['phoneNumber'] ?? '',
            interestedInPropertyId: $payload['interestedInPropertyId'] ?? null
        );

        $this->commandBus->dispatch($command);

        return new JsonResponse(null, Response::HTTP_ACCEPTED);
    }
}
