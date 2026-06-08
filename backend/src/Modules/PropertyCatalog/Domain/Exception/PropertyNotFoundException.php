<?php

declare(strict_types=1);

namespace App\Modules\PropertyCatalog\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

class PropertyNotFoundException extends DomainException
{
    public static function withMessage(string $message): self
    {
        return new self($message);
    }
}
