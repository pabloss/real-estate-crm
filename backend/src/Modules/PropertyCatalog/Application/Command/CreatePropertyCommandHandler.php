<?php

declare(strict_types=1);

namespace App\Modules\PropertyCatalog\Application\Command;

use App\Modules\PropertyCatalog\Domain\Entity\Property;
use App\Modules\PropertyCatalog\Domain\ValueObject\PropertyArea;
use App\Modules\PropertyCatalog\Domain\ValueObject\PropertyPrice;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
final readonly class CreatePropertyCommandHandler
{
    // Tutaj wstrzykniemy w przyszłości repozytorium do zapisu w bazie
    // public function __construct(private PropertyRepositoryInterface $repository) {}

    public function __invoke(CreatePropertyCommand $command): void
    {
        // Jeśli dane w $command są nieprawidłowe, na tym etapie polecą wyjątki InvalidArgumentException
        $property = new Property(
            Uuid::v4(),
            $command->title,
            new PropertyPrice($command->price), // cena w groszach
            new PropertyArea($command->area)
        );

        // Docelowo:
        // $this->repository->save($property);
    }
}
