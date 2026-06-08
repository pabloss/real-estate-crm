<?php

declare(strict_types=1);

namespace App\Modules\PropertyCatalog\Infrastructure\Controller;

use App\Modules\PropertyCatalog\Application\Command\CreatePropertyCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/properties', name: 'api_properties_')]
final class PropertyController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $commandBus
    ) {
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = $request->toArray();

        // Tworzymy komendę z payloadu JSON
        $command = new CreatePropertyCommand(
            title: $payload['title'] ?? '',
            price: $payload['price'] ?? 0,
            area: $payload['area'] ?? 0.0,
            type: $payload['type'] ?? 'apartment'
        );

        // Wrzucamy na szynę - CQRS w czystej postaci (brak zwracanych danych biznesowych)
        $this->commandBus->dispatch($command);

        return new JsonResponse(null, Response::HTTP_ACCEPTED);
    }
}
