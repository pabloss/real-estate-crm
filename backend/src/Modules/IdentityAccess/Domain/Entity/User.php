<?php

declare(strict_types=1);

namespace App\Modules\IdentityAccess\Domain\Entity;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    private Uuid $id;
    private string $email;
    private string $password;
    private array $roles;

    public function __construct(Uuid $id, string $email, string $password, array $roles = ['ROLE_USER'])
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->roles = $roles;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER'; // Gwarantujemy, że każdy ma przynajmniej tę rolę
        return array_unique($roles);
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function eraseCredentials(): void
    {
        // Jeśli przechowywalibyśmy jawne hasło tymczasowo, tutaj byśmy je usuwali
    }
}
