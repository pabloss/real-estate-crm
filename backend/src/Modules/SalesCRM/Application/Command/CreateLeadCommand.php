<?php

declare(strict_types=1);

namespace App\Modules\SalesCRM\Application\Command;

final readonly class CreateLeadCommand
{
    public function __construct(
        public string $fullName,
        public string $email,
        public string $phoneNumber,
        public ?string $interestedInPropertyId = null
    ) {
    }
}
