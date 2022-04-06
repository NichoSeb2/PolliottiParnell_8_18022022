<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use App\Controller\SecurityController;
use App\DataFixtures\UserFixturesTest;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;

class SecurityControllerTest extends WebTestCase {
    protected KernelBrowser $client;

    protected $databaseTool;

    protected UserRepository $userRepository;

    public function setUp(): void {
		parent::setUp();

        $this->client = static::createClient();

		$this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();

        $this->userRepository = self::getContainer()->get(UserRepository::class);
	}

    //* loginAction
    public function testAccessLoginWithSession(): void {
        $this->databaseTool->loadFixtures([UserFixturesTest::class]);

        $admin = $this->userRepository->findBy(['email' => "admin@example.fr"])[0];
        $this->client->loginUser($admin);

        $crawler = $this->client->request('GET', '/login');

        $this->assertResponseRedirects("/");
    }

    public function testLoginWithValidCredential(): void {
        $this->databaseTool->loadFixtures([UserFixturesTest::class]);

        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/login');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton("Se connecter")->form();

        $form["username"]->setValue("Admin");
        $form["password"]->setValue("8888");

        $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains("h1", "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !");
    }

    public function testLoginWithInvalidCredential(): void {
        $this->databaseTool->loadFixtures([UserFixturesTest::class]);

        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/login');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton("Se connecter")->form();

        $form["username"]->setValue("Admin");
        $form["password"]->setValue("0000");

        $this->client->submit($form);
        $this->assertSelectorTextContains('div[role="alert"]', "Invalid credentials.");
    }

    //* logout
    public function testLogoutException(): void {
        $controller = new SecurityController();

        $this->expectException(\LogicException::class);
        $controller->logout();
    }
}
