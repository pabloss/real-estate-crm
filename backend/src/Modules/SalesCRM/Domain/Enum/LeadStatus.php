<?php

declare(strict_types=1);

namespace App\Modules\SalesCRM\Domain\Enum;

enum LeadStatus: string
{
    case NEW = 'new';
    case CONTACTED = 'contacted';
    case QUALIFIED = 'qualified';
    case LOST = 'lost';
}
