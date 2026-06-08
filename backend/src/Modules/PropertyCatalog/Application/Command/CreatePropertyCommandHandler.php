<?php

declare(strict_types=1);

namespace App\Modules\PropertyCatalog\Application\Command;

use App\Modules\PropertyCatalog\Domain\Entity\Property;
use App\Modules\PropertyCatalog\Domain\Repository\PropertyRepositoryInterface;
use App\Modules\PropertyCatalog\Domain\ValueObject\PropertyArea;
use App\Modules\PropertyCatalog\Domain\ValueObject\PropertyPrice;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
final readonly class CreatePropertyCommandHandler
{
    public function __construct(
        private PropertyRepositoryInterface $repository
    ) {
    }

    public function __invoke(CreatePropertyCommand $command): void
    {
        $property = new Property(
            Uuid::v4(),
            $command->title,
            new PropertyPrice($command->price),
            new PropertyArea($command->area)
        );

        $this->repository->save($property);
    }
}
