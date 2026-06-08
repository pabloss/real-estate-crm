<?php

declare(strict_types=1);

namespace App\Modules\PropertyCatalog\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidDomainArgumentException;

final readonly class PropertyArea
{
    public function __construct(
        public float $squareMeters
    ) {
        if ($this->squareMeters <= 0.0) {
            throw InvalidDomainArgumentException::withMessage('Powierzchnia nieruchomości musi być większa od zera.');
        }
    }
}
