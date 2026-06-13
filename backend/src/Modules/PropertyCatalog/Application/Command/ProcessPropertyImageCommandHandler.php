<?php

declare(strict_types=1);

namespace App\Modules\PropertyCatalog\Application\Command;

use App\Modules\PropertyCatalog\Domain\Repository\PropertyRepositoryInterface;
use App\Modules\PropertyCatalog\Infrastructure\Service\ImageProcessor;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
final readonly class ProcessPropertyImageCommandHandler
{
    public function __construct(
        private PropertyRepositoryInterface $repository,
        private ImageProcessor $imageProcessor,
        private HubInterface $mercureHub // Wstrzykujemy Hub Mercure
    ) {
    }

    public function __invoke(ProcessPropertyImageCommand $command): void
    {
        $propertyId = Uuid::fromString($command->propertyId);
        $property = $this->repository->getById($propertyId);

        $targetFilename = sprintf('%s_%s.%s',
            $propertyId->toRfc4122(),
            time(),
            $command->originalExtension
        );

        $publicPath = $this->imageProcessor->processAndWatermark(
            $command->tmpFilePath,
            $targetFilename
        );

        // Zakładam istnienie tej metody w encji
        $property->setMainPhotoUrl($publicPath);
        $this->repository->save($property);

        // 1. Definiujemy Payload (dane dla frontendu w formacie JSON)
        $payload = json_encode([
            'propertyId' => $propertyId->toRfc4122(),
            'photoUrl' => $publicPath
        ], JSON_THROW_ON_ERROR);

        // 2. Tworzymy obiekt Update dla konkretnego tematu (Topic)
        $update = new Update(
            'http://crm.local/properties/photo-updated', // Identyfikator tematu
            $payload
        );

        // 3. Wypychamy do huba Mercure (Server-Sent Event leci do przeglądarki!)
        $this->mercureHub->publish($update);
    }
}
