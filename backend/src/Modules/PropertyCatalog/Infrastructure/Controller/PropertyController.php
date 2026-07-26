<?php

declare(strict_types=1);

namespace App\Modules\PropertyCatalog\Infrastructure\Controller;

use App\Modules\PropertyCatalog\Application\Command\CreatePropertyCommand;
use App\Modules\PropertyCatalog\Application\Command\ProcessPropertyImageCommand;
use App\Modules\PropertyCatalog\Application\Query\GetPropertiesQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/properties', name: 'api_properties_')]
final class PropertyController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        private readonly MessageBusInterface $commandBus,
        MessageBusInterface $queryBus, // Wstrzykujemy szynę zapytań dla HandleTrait
    ) {
        $this->messageBus = $queryBus; // Inicjalizacja dla HandleTrait
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $query = new GetPropertiesQuery();

        // HandleTrait dostarcza metodę handle(), która rozpakowuje Envelope z Messengera
        $properties = $this->handle($query);

        return $this->json($properties);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = $request->toArray();

        $command = new CreatePropertyCommand(
            title: $payload['title'] ?? '',
            price: $payload['price'] ?? 0,
            area: $payload['area'] ?? 0.0,
            type: $payload['type'] ?? 'apartment'
        );

        $this->commandBus->dispatch($command);

        return new JsonResponse(null, Response::HTTP_ACCEPTED);
    }

    #[Route('/{id}/photo', name: 'upload_photo', methods: ['POST'])]
    public function uploadPhoto(string $id, Request $request): JsonResponse
    {
        /** @var UploadedFile|null $file */
        $file = $request->files->get('photo');

        if (!$file) {
            return new JsonResponse(['error' => 'Brak pliku'], Response::HTTP_BAD_REQUEST);
        }

        // Zabezpieczenie przed wgrywaniem niebezpiecznych skryptów
        if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'])) {
            return new JsonResponse(['error' => 'Nieprawidłowy format pliku'], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }

        // Zapis do ukrytego, tymczasowego folderu (niedostępnego z przeglądarki)
        $tmpDir = $this->getParameter('kernel.project_dir').'/var/uploads/tmp';
        $extension = $file->guessExtension();
        $tmpFilename = uniqid('tmp_img_', true).'.'.$extension;
        $file->move($tmpDir, $tmpFilename);
        $tmpFilePath = $tmpDir.'/'.$tmpFilename;

        // Wrzucamy komendę do tła
        $this->commandBus->dispatch(new ProcessPropertyImageCommand(
            $id,
            $tmpFilePath,
            $extension
        ));

        // Natychmiastowa odpowiedź dla frontendu, mimo że plik dopiero będzie przetwarzany
        return new JsonResponse(['message' => 'Zdjęcie w trakcie przetwarzania.'], Response::HTTP_ACCEPTED);
    }
}
