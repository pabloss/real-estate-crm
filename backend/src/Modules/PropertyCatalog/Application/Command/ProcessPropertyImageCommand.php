<?php

declare(strict_types=1);

namespace App\Modules\PropertyCatalog\Application\Command;

final readonly class ProcessPropertyImageCommand
{
    public function __construct(
        public string $propertyId,
        public string $tmpFilePath,
        public string $originalExtension,
    ) {
    }
}
