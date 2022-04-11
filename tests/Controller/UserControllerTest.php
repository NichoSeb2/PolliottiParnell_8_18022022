<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use App\DataFixtures\UserFixturesTest;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;

class UserControllerTest extends WebTestCase {
    protected KernelBrowser $client;

    protected $databaseTool;

    protected UserRepository $userRepository;

    public function setUp(): void {
		parent::setUp();

        $this->client = static::createClient();

		$this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();

		$this->userRepository = self::getContainer()->get(UserRepository::class);
	}

    //* listAction
    public function testAccessListUserWithoutSession(): void {
        $crawler = $this->client->request('GET', '/users');
        $this->assertResponseRedirects("/login");
    }

    public function testAccessListUserWithoutPermission(): void {
        $this->databaseTool->loadFixtures([UserFixturesTest::class]);

        $this->client->followRedirects();

        $user = $this->userRepository->findBy(['email' => "user@example.fr"])[0];
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/users');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAccessListUserWithPermission(): void {
        $this->databaseTool->loadFixtures([UserFixturesTest::class]);

        $this->client->followRedirects();

        $admin = $this->userRepository->findBy(['email' => "admin@example.fr"])[0];
        $this->client->loginUser($admin);

        $crawler = $this->client->request('GET', '/users');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    //* createAction
    public function testAccessCreateUserWithSession(): void {
        $this->databaseTool->loadFixtures([UserFixturesTest::class]);

        $admin = $this->userRepository->findBy(['email' => "admin@example.fr"])[0];
        $this->client->loginUser($admin);

        $crawler = $this->client->request('GET', '/users/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateUserWithSessionAndValidData(): void {
        $this->databaseTool->loadFixtures([UserFixturesTest::class]);

        $admin = $this->userRepository->findBy(['email' => "admin@example.fr"])[0];
        $this->client->loginUser($admin);

        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/users/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton("Ajouter")->form();

        $form["user[username]"]->setValue("Test name");
        $form["user[password][first]"]->setValue("0000");
        $form["user[password][second]"]->setValue("0000");
        $form["user[email]"]->setValue("test@test.test");

        $this->client->submit($form);

        $this->assertSelectorTextContains('div[role="alert"]', "L'utilisateur a bien été ajouté.");
    }

    public function testCreateUserWithSessionAndInvalidData(): void {
        $this->databaseTool->loadFixtures([UserFixturesTest::class]);

        $admin = $this->userRepository->findBy(['email' => "admin@example.fr"])[0];
        $this->client->loginUser($admin);

        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/users/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton("Ajouter")->form();

        $this->client->submit($form);

        if ($profile = $this->client->getProfile()) {
            $this->assertEquals(4, $profile->getCollector("validator")->getViolationsCount());
        }
    }

    //* editAction
    public function testEditUserWithSessionAndWrongUserId(): void {
        $this->databaseTool->loadFixtures([UserFixturesTest::class]);

        $admin = $this->userRepository->findBy(['email' => "admin@example.fr"])[0];
        $this->client->loginUser($admin);

        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/users/0/edit');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testEditUserWithSessionAndValidData(): void {
        $this->databaseTool->loadFixtures([UserFixturesTest::class]);

        $admin = $this->userRepository->findBy(['email' => "admin@example.fr"])[0];
        $this->client->loginUser($admin);

        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/users/1/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton("Modifier")->form();

        $this->assertEquals("Admin", $form["user[username]"]->getValue());
        $this->assertEquals("admin@example.fr", $form["user[email]"]->getValue());

        $form["user[username]"]->setValue("Test name edit");
        $form["user[email]"]->setValue("test@test.test");

        $this->client->submit($form);

        $this->assertSelectorTextContains('div[role="alert"]', "L'utilisateur a bien été modifié.");
    }

    public function testEditUserWithSessionSaveWithoutEdit(): void {
        $this->databaseTool->loadFixtures([UserFixturesTest::class]);

        $admin = $this->userRepository->findBy(['email' => "admin@example.fr"])[0];
        $this->client->loginUser($admin);

        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/users/1/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton("Modifier")->form();

        $this->assertEquals("Admin", $form["user[username]"]->getValue());
        $this->assertEquals("admin@example.fr", $form["user[email]"]->getValue());

        $this->client->submit($form);

        $this->assertSelectorTextContains('div[role="alert"]', "L'utilisateur a bien été modifié.");
    }

    public function testEditUserWithSessionAndInvalidData(): void {
        $this->databaseTool->loadFixtures([UserFixturesTest::class]);

        $admin = $this->userRepository->findBy(['email' => "admin@example.fr"])[0];
        $this->client->loginUser($admin);

        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/users/1/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton("Modifier")->form();

        $this->assertEquals("Admin", $form["user[username]"]->getValue());
        $this->assertEquals("admin@example.fr", $form["user[email]"]->getValue());

        $form["user[email]"]->setValue("test");

        $this->client->submit($form);

        if ($profile = $this->client->getProfile()) {
            $this->assertEquals(1, $profile->getCollector("validator")->getViolationsCount());
        }
    }
}
