<?php

declare(strict_types=1);

namespace App\Modules\PropertyCatalog\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidDomainArgumentException;

final readonly class PropertyPrice
{
    public function __construct(
        public int $amountInCents
    ) {
        if ($this->amountInCents < 0) {
            throw InvalidDomainArgumentException::withMessage('Cena nieruchomości nie może być ujemna.');
        }
    }
}
