<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

use DomainException as BaseDomainException;

abstract class DomainException extends BaseDomainException
{
    // Jako klasa abstrakcyjna służy głównie do typowania (polimorfizmu)
    // w naszym Event Listenerze. Możemy tu w przyszłości dodać np. kody błędów.
}
