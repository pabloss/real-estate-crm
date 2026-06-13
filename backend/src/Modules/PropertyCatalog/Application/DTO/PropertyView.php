<?php

declare(strict_types=1);

namespace App\Modules\PropertyCatalog\Application\DTO;

final readonly class PropertyView
{
    public function __construct(
        public string $id,
        public string $title,
        public int $priceInCents,
        public float $areaSquareMeters,
        public int $interestedLeadsCount = 0, // Dodane pole
        public ?string $mainPhotoUrl,
    ) {
    }
}
