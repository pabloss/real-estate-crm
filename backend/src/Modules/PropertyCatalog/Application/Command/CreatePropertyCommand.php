<?php

declare(strict_types=1);

namespace App\Modules\PropertyCatalog\Application\Command;

final readonly class CreatePropertyCommand
{
    public function __construct(
        public string $title,
        public int $price,
        public float $area,
        public string $type
    ) {
    }
}
