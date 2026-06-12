<?php

declare(strict_types=1);

namespace App\Modules\SalesCRM\Application\DTO;

final readonly class LeadView
{
    public function __construct(
        public string $id,
        public string $fullName,
        public string $phoneNumber,
        public string $status,
        public ?string $interestedInPropertyId,
        public string $email,
    ) {
    }
}
