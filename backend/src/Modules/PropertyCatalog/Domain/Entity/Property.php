<?php

declare(strict_types=1);

namespace App\Modules\PropertyCatalog\Domain\Entity;

use App\Modules\PropertyCatalog\Domain\ValueObject\PropertyArea;
use App\Modules\PropertyCatalog\Domain\ValueObject\PropertyPrice;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

class Property
{
    private Uuid $id;
    private string $title;
    private PropertyPrice $price;
    private PropertyArea $area;

    public function __construct(
        Uuid $id,
        string $title,
        PropertyPrice $price,
        PropertyArea $area
    ) {
        $this->id = $id;
        $this->setValidTitle($title);
        $this->price = $price;
        $this->area = $area;
    }

    private function setValidTitle(string $title): void
    {
        $trimmedTitle = trim($title);
        if ($trimmedTitle === '') {
            throw new InvalidArgumentException('Tytuł nieruchomości nie może być pusty.');
        }
        if (mb_strlen($trimmedTitle) < 5) {
            throw new InvalidArgumentException('Tytuł nieruchomości musi mieć minimum 5 znaków.');
        }

        $this->title = $trimmedTitle;
    }

    // Tutaj znajdą się gettery (jeśli są potrzebne) oraz metody zmieniające stan
    // np. public function changePrice(PropertyPrice $newPrice): void
}
