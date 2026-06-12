<?php

declare(strict_types=1);

namespace App\Modules\PropertyCatalog\Application\Command;

use App\Modules\PropertyCatalog\Domain\Repository\PropertyRepositoryInterface;
use App\Modules\PropertyCatalog\Infrastructure\Service\ImageProcessor;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
final readonly class ProcessPropertyImageCommandHandler
{
    public function __construct(
        private PropertyRepositoryInterface $repository,
        private ImageProcessor $imageProcessor
    ) {
    }

    public function __invoke(ProcessPropertyImageCommand $command): void
    {
        $propertyId = Uuid::fromString($command->propertyId);
        $property = $this->repository->getById($propertyId);

        // Unikalna nazwa pliku
        $targetFilename = sprintf('%s_%s.%s',
            $propertyId->toRfc4122(),
            time(),
            $command->originalExtension
        );

        // Przetwarzanie obrazu
        $publicPath = $this->imageProcessor->processAndWatermark(
            $command->tmpFilePath,
            $targetFilename
        );

        // Zakładam, że w Encji Property dodałeś metodę setMainPhoto()
        $property->setMainPhotoUrl($publicPath);

        $this->repository->save($property);
    }
}
