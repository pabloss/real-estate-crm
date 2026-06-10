<?php

declare(strict_types=1);

namespace App\Modules\SalesCRM\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidDomainArgumentException;

final readonly class Email
{
    public function __construct(
        public string $value
    ) {
        if (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            throw InvalidDomainArgumentException::withMessage('Nieprawidłowy format adresu e-mail.');
        }
    }
}
