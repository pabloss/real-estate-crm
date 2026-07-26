<?php

declare(strict_types=1);

namespace App\Modules\IdentityAccess\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

final class SecurityController extends AbstractController
{
    #[Route('/api/login_check', name: 'api_login_check', methods: ['POST'])]
    public function loginCheck(): void
    {
        // Ta metoda fizycznie nigdy się nie wykona, ponieważ
        // LexikJWTAuthenticator przechwyci to żądanie na poziomie zapory (Firewall).
        // Umieszczamy tu wyjątek na wypadek, gdyby konfiguracja firewalla została popsuta.
        throw new \LogicException('Ta metoda powinna zostać przechwycona przez firewall LexikJWT.');
    }
}
