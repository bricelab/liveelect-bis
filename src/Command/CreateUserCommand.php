<?php

namespace App\Command;

use App\Repository\UtilisateurRepository;
use App\Service\CreateNewUserAccount;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validation;

#[AsCommand(
    name: 'app:create-user',
    description: 'Add a short description for your command',
)]
class CreateUserCommand extends Command
{
//    public function __construct(
//        private readonly CreateNewUserAccount $createNewUserAccount,
//        private readonly UtilisateurRepository $repository,
//        string $name = null
//    ) {
//        parent::__construct($name);
//    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $lastname */
        $lastname = $io->ask('Nom ', null, function ($answer) {
            if (!$answer) {
                throw new RuntimeException('Le nom ne doit pas être vide.');
            }

            return strtoupper($answer);
        });

        /** @var string $firstname */
        $firstname = $io->ask('Prénom(s) ', null, function ($answer) {
            if (!$answer) {
                throw new RuntimeException('Le(s) prénom(s) ne doit(vent) pas être vide(s).');
            }

            return ucfirst($answer);
        });

        /** @var string $password */
        $email = $io->ask('Email ', null, function ($answer) {
            return $this->validateEmail($answer);
        });

        /** @var string $password */
        $password = $io->askHidden('Mot de passe ', function ($answer) {
            return $this->validatePassword($answer);
        });

        $isAdmin = false;
        $isSuperAdmin = $io->confirm('Super administrateur ? ');

        if (!$isSuperAdmin) {
            $isAdmin = $io->confirm('Administrateur ? ');
        }

        $roles = $isSuperAdmin ? ['ROLE_SUPER_ADMIN', 'ROLE_ADMIN'] : ($isAdmin ? ['ROLE_ADMIN'] : []);
        $roles[] = 'ROLE_USER';

        $user = $this->createNewUserAccount->create($email, $password, $roles, $lastname, $firstname);

        $io->success(
            sprintf(
                '👍 Utilisateur "%s %s (%s)" ajouté avec succès.',
                $user->getNom(),
                $user->getPrenoms(),
                $user->getEmail()
            )
        );

        return Command::SUCCESS;
    }

    private function validateEmail(?string $answer): string
    {
        if (!$answer) {
            throw new RuntimeException('Entrer une adresse mail valide.');
        }

        if (Validation::createValidator()->validate($answer, [new Email()])->count() > 0) {
            throw new RuntimeException('Entrer une adresse mail valide.');
        }

        $doublon = $this->repository->findOneBy(['email' => $answer]);

        if ($doublon) {
            throw new RuntimeException('Email déjà utilisé par un autre utilisateur.');
        }

        return $answer;
    }

    private function validatePassword(?string $answer): string
    {
        if (!$answer) {
            throw new RuntimeException('Le mot de passe ne doit pas être vide.');
        }

        if (
            Validation::createValidator()->validate(
                $answer,
                [new Regex('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-+!*$@%_])([-+!*$@%_\w]{8,})$/')]
            )->count() > 0
        ) {
            throw new RuntimeException('Le mot de passe ne respecte pas la complexité minimale.');
        }

        return $answer;
    }
}
