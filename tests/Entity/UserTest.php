<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase {
	private Task $validTask;

	public function setUp(): void {
		parent::setUp();

		static::bootKernel();

		$this->validTask = (new Task())
			->setTitle("Test title")
			->setContent("Test content")
			->toggle(true)
        ;
	}

    private function getValidEntity(): User {
        return (new User())
			->setUsername("Test username")
			->setEmail("test@test.test")
			->setPassword("0000")
			->setRoles(['ROLE_USER'])
			->addTask($this->validTask)
        ;
    }

	public function testAddTask(): void {
        $user = $this->getValidEntity();

		$task = (new Task)
			->setTitle("Test title 2")
			->setContent("Test content 2")
			->toggle(false)
		;

		$user->addTask($task);

        $this->assertCount(2, $user->getTasks());
    }

	public function testRemoveTask(): void {
        $user = $this->getValidEntity();

		$user->removeTask($this->validTask);

        $this->assertCount(0, $user->getTasks());
    }
}
