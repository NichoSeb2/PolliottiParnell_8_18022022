<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\DataFixtures\TaskFixturesTest;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;

class TaskControllerTest extends WebTestCase {
    protected KernelBrowser $client;

    protected $databaseTool;

    protected UserRepository $userRepository;
    protected TaskRepository $taskRepository;

    public function setUp(): void {
		parent::setUp();

        $this->client = static::createClient();

		$this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();

		$this->userRepository = self::getContainer()->get(UserRepository::class);
		$this->taskRepository = self::getContainer()->get(TaskRepository::class);
	}

    //* listAction
    public function testAccessListTaskWithoutSession(): void {
        $crawler = $this->client->request('GET', '/');

        $this->assertResponseRedirects("/login");
    }

    public function testAccessListTaskWithSession(): void {
        $this->databaseTool->loadFixtures([TaskFixturesTest::class]);

        $admin = $this->userRepository->findBy(['email' => "admin@example.fr"])[0];
        $this->client->loginUser($admin);

        $crawler = $this->client->request('GET', '/tasks');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        foreach ($admin->getTasks() as $key => $value) {
            $this->assertSelectorTextContains('a[href="/tasks/'. $value->getId(). '/edit"]', $value->getTitle());
        }
    }

    //* createAction
    public function testAccessCreateTaskWithSession(): void {
        $this->databaseTool->loadFixtures([TaskFixturesTest::class]);

        $admin = $this->userRepository->findBy(['email' => "admin@example.fr"])[0];
        $this->client->loginUser($admin);

        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateTaskWithSessionAndValidData(): void {
        $this->databaseTool->loadFixtures([TaskFixturesTest::class]);

        $admin = $this->userRepository->findBy(['email' => "admin@example.fr"])[0];
        $this->client->loginUser($admin);

        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton("Ajouter")->form();

        $form["task[title]"]->setValue("Test title");
        $form["task[content]"]->setValue("Test content");

        $this->client->submit($form);

        $this->assertSelectorTextContains('div[role="alert"]', "La tâche a été bien été ajoutée.");
    }

    public function testCreateTaskWithSessionAndInvalidData(): void {
        $this->databaseTool->loadFixtures([TaskFixturesTest::class]);

        $admin = $this->userRepository->findBy(['email' => "admin@example.fr"])[0];
        $this->client->loginUser($admin);

        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton("Ajouter")->form();

        $this->client->submit($form);

        //TODO: Assert form errors
    }

    //* editAction
    public function testEditTaskWithSessionAndWrongTaskId(): void {
        $this->databaseTool->loadFixtures([TaskFixturesTest::class]);

        $admin = $this->userRepository->findBy(['email' => "admin@example.fr"])[0];
        $this->client->loginUser($admin);

        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/tasks/0/edit');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testEditTaskWithSessionAndValidData(): void {
        $this->databaseTool->loadFixtures([TaskFixturesTest::class]);

        $admin = $this->userRepository->findBy(['email' => "admin@example.fr"])[0];
        $this->client->loginUser($admin);

        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/tasks/1/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton("Modifier")->form();

        $this->assertEquals("Task 0 admin", $form["task[title]"]->getValue());
        $this->assertEquals("Content", $form["task[content]"]->getValue());

        $form["task[title]"]->setValue("Test title edit");
        $form["task[content]"]->setValue("Test content edit");

        $this->client->submit($form);

        $this->assertSelectorTextContains('div[role="alert"]', "La tâche a bien été modifiée.");
    }

    //* toggleTaskAction
    public function testToggleTaskWithSessionAndWrongTaskId(): void {
        $this->databaseTool->loadFixtures([TaskFixturesTest::class]);

        $admin = $this->userRepository->findBy(['email' => "admin@example.fr"])[0];
        $this->client->loginUser($admin);

        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/tasks/0/toggle');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testToggleTaskWithSession(): void {
        $this->databaseTool->loadFixtures([TaskFixturesTest::class]);

        $admin = $this->userRepository->findBy(['email' => "admin@example.fr"])[0];
        $this->client->loginUser($admin);

        $this->client->followRedirects();

        $task = $this->taskRepository->find(1);
        $this->assertFalse($task->isDone());

        $crawler = $this->client->request('GET', '/tasks/1/toggle');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $task = $this->taskRepository->find(1);
        $this->assertTrue($task->isDone());

        $this->assertSelectorTextContains('div[role="alert"]', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));
    }

    //* deleteTaskAction
    public function testDeleteTaskWithSessionAndWrongTaskId(): void {
        $this->databaseTool->loadFixtures([TaskFixturesTest::class]);

        $admin = $this->userRepository->findBy(['email' => "admin@example.fr"])[0];
        $this->client->loginUser($admin);

        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/tasks/0/delete');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteTaskWithSession(): void {
        $this->databaseTool->loadFixtures([TaskFixturesTest::class]);

        $admin = $this->userRepository->findBy(['email' => "admin@example.fr"])[0];
        $this->client->loginUser($admin);

        $this->client->followRedirects();

        $task = $this->taskRepository->find(1);
        $this->assertInstanceOf(Task::class, $task);

        $crawler = $this->client->request('GET', '/tasks/1/delete');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $task = $this->taskRepository->find(1);
        $this->assertNull($task);
    }

    public function testDeleteTaskWithWrongSession(): void {
        $this->databaseTool->loadFixtures([TaskFixturesTest::class]);

        $admin = $this->userRepository->findBy(['email' => "user@example.fr"])[0];
        $this->client->loginUser($admin);

        $this->client->followRedirects();

        $task = $this->taskRepository->find(1);
        $this->assertInstanceOf(Task::class, $task);

        $crawler = $this->client->request('GET', '/tasks/1/delete');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteAnonymousTaskWithUserSession(): void {
        $this->databaseTool->loadFixtures([TaskFixturesTest::class]);

        $user = $this->userRepository->findBy(['email' => "user@example.fr"])[0];
        $anonymous = $this->userRepository->findBy(['email' => "anonymous@example.fr"])[0];
        $this->client->loginUser($user);

        $this->client->followRedirects();

        $task = $this->taskRepository->find(1);
        $this->assertInstanceOf(Task::class, $anonymous->getTasks()[0]);

        $crawler = $this->client->request('GET', '/tasks/'. $anonymous->getTasks()[0]->getId(). '/delete');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteAnonymousTaskWithAdminSession(): void {
        $this->databaseTool->loadFixtures([TaskFixturesTest::class]);

        $admin = $this->userRepository->findBy(['email' => "admin@example.fr"])[0];
        $anonymous = $this->userRepository->findBy(['email' => "anonymous@example.fr"])[0];
        $this->client->loginUser($admin);

        $this->client->followRedirects();

        $task = $this->taskRepository->find(1);
        $this->assertInstanceOf(Task::class, $anonymous->getTasks()[0]);

        $crawler = $this->client->request('GET', '/tasks/'. $anonymous->getTasks()[0]->getId(). '/delete');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
