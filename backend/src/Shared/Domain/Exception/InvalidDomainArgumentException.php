<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

final class InvalidDomainArgumentException extends DomainException
{
    public static function withMessage(string $message): self
    {
        return new self($message);
    }
}
