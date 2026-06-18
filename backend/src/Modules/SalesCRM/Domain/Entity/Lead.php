<?php

declare(strict_types=1);

namespace App\Modules\SalesCRM\Domain\Entity;

use App\Modules\SalesCRM\Domain\Enum\LeadStatus;
use App\Modules\SalesCRM\Domain\Event\LeadCreated;
use App\Modules\SalesCRM\Domain\ValueObject\Email;
use App\Shared\Domain\Aggregate\AggregateRootTrait;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

class Lead
{
    // Dołączamy mechanizm logowania zdarzeń
    use AggregateRootTrait;

    private Uuid $id;
    private string $fullName;
    private Email $email;
    private string $phoneNumber;
    private LeadStatus $status;
    private ?Uuid $interestedInPropertyId;

    public function __construct(
        Uuid $id,
        string $fullName,
        Email $email,
        string $phoneNumber,
        ?Uuid $interestedInPropertyId = null
    ) {
        $this->id = $id;
        $this->setValidFullName($fullName);
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
        $this->status = LeadStatus::NEW;
        $this->interestedInPropertyId = $interestedInPropertyId;

        // Rejestrujemy zdarzenie domenowe!
        $this->record(new LeadCreated(
            (string) $this->id,
            $this->interestedInPropertyId?->toRfc4122(),
            new \DateTimeImmutable()
        ));
    }

    private function setValidFullName(string $fullName): void
    {
        $trimmed = trim($fullName);
        if (mb_strlen($trimmed) < 3) {
            throw new InvalidArgumentException('Imię i nazwisko musi mieć min. 3 znaki.');
        }
        $this->fullName = $trimmed;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    // Metody mutujące stan
    public function markAsContacted(): void
    {
        $this->status = LeadStatus::CONTACTED;
    }
}
