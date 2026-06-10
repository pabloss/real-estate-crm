<?php

declare(strict_types=1);

namespace App\Modules\SalesCRM\Domain\Entity;

use App\Modules\SalesCRM\Domain\Enum\LeadStatus;
use App\Modules\SalesCRM\Domain\ValueObject\Email;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

class Lead
{
    private Uuid $id;
    private string $fullName;
    private Email $email;
    private string $phoneNumber;
    private LeadStatus $status;
    private ?Uuid $interestedInPropertyId; // Miękkie powiązanie z PropertyCatalog!

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
        $this->status = LeadStatus::NEW; // Domyślny status przy tworzeniu
        $this->interestedInPropertyId = $interestedInPropertyId;
    }

    private function setValidFullName(string $fullName): void
    {
        $trimmed = trim($fullName);
        if (mb_strlen($trimmed) < 3) {
            throw new InvalidArgumentException('Imię i nazwisko musi mieć min. 3 znaki.');
        }
        $this->fullName = $trimmed;
    }

    // Metody mutujące stan
    public function markAsContacted(): void
    {
        $this->status = LeadStatus::CONTACTED;
    }
}
