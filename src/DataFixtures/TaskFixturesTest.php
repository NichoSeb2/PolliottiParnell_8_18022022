<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TaskFixturesTest extends Fixture {
    public function load(ObjectManager $manager): void {
        $admin = (new User)
            ->setUsername("Admin")
            ->setEmail("admin@example.fr")
            ->setPassword("8888")
            ->setRoles(['ROLE_ADMIN'])
        ;
        $manager->persist($admin);

        for ($i = 0; $i < 10; $i++) { 
            $task = new Task();
            $task
                ->setTitle("Task $i admin")
                ->setContent("Content")
                ->setUser($admin)
            ;
            $manager->persist($task);
        }

        $user = (new User)
            ->setUsername("User")
            ->setEmail("user@example.fr")
            ->setPassword("0000")
            ->setRoles(['ROLE_USER'])
        ;
        $manager->persist($user);

        for ($i = 0; $i < 10; $i++) { 
            $task = new Task();
            $task
                ->setTitle("Task $i user")
                ->setContent("Content")
                ->setUser($user)
            ;
            $manager->persist($task);
        }

        $anonymous = (new User)
            ->setUsername("Anonymous")
            ->setEmail("anonymous@example.fr")
            ->setPassword("0000")
            ->setRoles(['ROLE_ANONYMOUS'])
        ;
        $manager->persist($anonymous);

        for ($i = 0; $i < 10; $i++) { 
            $task = new Task();
            $task
                ->setTitle("Task $i anonymous")
                ->setContent("Content")
                ->setUser($anonymous)
            ;
            $manager->persist($task);
        }

        $manager->flush();
    }
}
