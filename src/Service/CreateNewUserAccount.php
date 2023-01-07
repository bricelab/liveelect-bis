<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\AdminUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class CreateNewUserAccount
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly UserPasswordHasherInterface $encoder)
    {
    }

    public function create($email, $password, $role, $nom = null, $prenoms = null): AdminUser
    {
        $user = new AdminUser();
        $user
            ->setEmail($email)
            ->setPassword($this->encoder->hashPassword($user, $password))
            ->setNom($nom)
            ->setPrenoms($prenoms)
            ->setRoles($role)
        ;

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
