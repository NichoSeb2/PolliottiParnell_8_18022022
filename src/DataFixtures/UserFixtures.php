<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements OrderedFixtureInterface, FixtureGroupInterface {
	public const ADMIN_REFERENCE = "admin";
	public const ANONYMOUS_REFERENCE = "anonymous";
	public const USER_REFERENCE = "user";

    public function __construct(private UserPasswordHasherInterface $passwordEncoder) {}

	public function getOrder(): int {
		return 1;
	}

	public static function getGroups(): array {
        return ['samples_data'];
    }

    public function load(ObjectManager $manager): void {
		$admin = new User();
        $admin
			->setUsername("Administrateur")
			->setEmail("admin@example.com")
			->setPassword($this->passwordEncoder->hashPassword(
				$admin,
				"Password"
			))
			->setRoles(["ROLE_ADMIN", "ROLE_USER"])
		;

		$this->addReference(self::ADMIN_REFERENCE, $admin);
		$manager->persist($admin);

		$anonymous = (new User())
			->setUsername("Anonyme")
			->setEmail("anonymous@example.com")
			->setPassword("anonyme")
			->setRoles(["ROLE_ANONYMOUS"])
		;

		$this->addReference(self::ANONYMOUS_REFERENCE, $anonymous);
		$manager->persist($anonymous);

		$user = new User();
		$user
			->setUsername("John Doe")
			->setEmail("john.doe@example.com")
			->setPassword($this->passwordEncoder->hashPassword(
				$user,
				"Password"
			))
			->setRoles(["ROLE_USER"])
		;

		$this->addReference(self::USER_REFERENCE, $user);
		$manager->persist($user);

		$manager->flush();
    }
}
