<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\DataFixtures\UserFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TaskFixtures extends Fixture implements OrderedFixtureInterface, FixtureGroupInterface {
    public function __construct(private UserPasswordHasherInterface $passwordEncoder) {}

	public function getOrder(): int {
		return 2;
	}

	public static function getGroups(): array {
        return ['samples_data'];
    }

    public function load(ObjectManager $manager): void {
        for ($i = 1; $i <= 5; $i++) { 
            $task = new Task();
            $task
                ->setTitle("Tache de l'admin n°$i")
                ->setContent("Contenue de la tache admin n°$i")
                ->setUser($this->getReference(UserFixtures::ADMIN_REFERENCE))
            ;

            if ($i % 2 == 0) {
                $task->toggle(true);
            }

            $manager->persist($task);
            $manager->flush();
        }

        for ($i = 1; $i <= 5; $i++) { 
            $task = new Task();
            $task
                ->setTitle("Tache de l'anonyme n°$i")
                ->setContent("Contenue de la tache anonyme n°$i")
                ->setUser($this->getReference(UserFixtures::ANONYMOUS_REFERENCE))
            ;

            if ($i % 2 == 0) {
                $task->toggle(true);
            }

            $manager->persist($task);
            $manager->flush();
        }

        for ($i = 1; $i <= 5; $i++) { 
            $task = new Task();
            $task
                ->setTitle("Tache de John n°$i")
                ->setContent("Contenue de la tache de John n°$i")
                ->setUser($this->getReference(UserFixtures::USER_REFERENCE))
            ;

            if ($i % 2 == 1) {
                $task->toggle(true);
            }

            $manager->persist($task);
            $manager->flush();
        }
    }
}
