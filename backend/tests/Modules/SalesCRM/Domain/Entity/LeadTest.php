<?php

declare(strict_types=1);

namespace App\Tests\Modules\SalesCRM\Domain\Entity;

use App\Modules\SalesCRM\Domain\Entity\Lead;
use App\Modules\SalesCRM\Domain\Event\LeadCreated;
use App\Modules\SalesCRM\Domain\ValueObject\Email;
use App\Shared\Domain\Exception\InvalidDomainArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class LeadTest extends TestCase
{
    public function testItCreatesLeadAndRecordsDomainEvent(): void
    {
        // Arrange
        $id = Uuid::v4();
        $propertyId = Uuid::v4();
        $email = new Email('test@crm.local');

        // Act
        $lead = new Lead($id, 'Jan Kowalski', $email, '123456789', $propertyId);

        // Assert
        $events = $lead->releaseEvents();
        $this->assertCount(1, $events, 'Zarejestrowano nieprawidłową liczbę zdarzeń.');

        $event = $events[0];
        $this->assertInstanceOf(LeadCreated::class, $event);
        $this->assertEquals((string) $id, $event->leadId);
        $this->assertEquals($propertyId->toRfc4122(), $event->interestedInPropertyId);
    }

    public function testItThrowsExceptionOnInvalidEmail(): void
    {
        // Assert
        $this->expectException(InvalidDomainArgumentException::class);
        $this->expectExceptionMessage('Nieprawidłowy format adresu e-mail.');

        // Act
        new Email('to-nie-jest-email');
    }

    public function testItThrowsExceptionOnTooShortName(): void
    {
        // Assert
        $this->expectException(\InvalidArgumentException::class);

        // Act
        new Lead(Uuid::v4(), 'Ja', new Email('test@crm.local'), '123456789');
    }
}
