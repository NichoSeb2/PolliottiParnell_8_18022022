<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use App\DataFixtures\TaskFixturesTest;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;

class DefaultControllerTest extends WebTestCase {
    protected KernelBrowser $client;

    protected UserRepository $userRepository;

    public function setUp(): void {
		parent::setUp();

        $this->client = static::createClient();

		$databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
		$databaseTool->loadFixtures([TaskFixturesTest::class]);

		$this->userRepository = self::getContainer()->get(UserRepository::class);
	}

    //* indexAction
    public function testAccessHomeWithoutSession(): void {
        $crawler = $this->client->request('GET', '/');

        $this->assertResponseRedirects("/login");
    }

    public function testAccessHomeWithSession(): void {
        $this->client->loginUser($this->userRepository->findBy(['email' => "admin@example.fr"])[0]);

        $crawler = $this->client->request('GET', '/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains("h1", "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !");
    }
}
