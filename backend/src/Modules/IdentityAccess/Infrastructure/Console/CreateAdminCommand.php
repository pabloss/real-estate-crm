<?php

declare(strict_types=1);

namespace App\Modules\IdentityAccess\Infrastructure\Console;

use App\Modules\IdentityAccess\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

#[AsCommand(name: 'app:create-admin', description: 'Tworzy domyślnego użytkownika administratora.')]
final class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = 'admin@crm.local';

        $user = new User(Uuid::v4(), $email, '', ['ROLE_ADMIN']);
        // Haszujemy hasło "password"
        $hashedPassword = $this->hasher->hashPassword($user, 'password');

        $user = new User($user->getId(), $email, $hashedPassword, ['ROLE_ADMIN']);

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln("Utworzono administratora: {$email} / password");

        return Command::SUCCESS;
    }
}
