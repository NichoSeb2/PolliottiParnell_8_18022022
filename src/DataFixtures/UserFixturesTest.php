<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixturesTest extends Fixture {
    public function __construct(private UserPasswordHasherInterface $passwordEncoder) {}

    public function load(ObjectManager $manager): void {
        $admin = new User();
        $admin
            ->setUsername("Admin")
            ->setEmail("admin@example.fr")
            ->setPassword($this->passwordEncoder->hashPassword($admin, "8888"))
            ->setRoles(["ROLE_USER", "ROLE_ADMIN"])
        ;
        $manager->persist($admin);

        $user = new User();
        $user
            ->setUsername("User")
            ->setEmail("user@example.fr")
            ->setPassword($this->passwordEncoder->hashPassword($user, "0000"))
            ->setRoles(["ROLE_USER"])
        ;
        $manager->persist($user);

        $manager->flush();
    }
}
