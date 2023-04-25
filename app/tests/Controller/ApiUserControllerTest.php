<?php

namespace App\Test\Controller;

use App\Entity\AccessToken;
use App\Entity\User;
use App\Repository\AccessTokenRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiUserControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    /**
     * @throws \Exception if the doctrine service could not be found
     */
    protected function setUp(): void
    {
        $this->client = $this->createClient();
        $repository = static::getContainer()->get('doctrine')->getRepository(User::class);

        foreach ($repository->findAll() as $object) {
            $repository->remove($object, true);
        }
    }

    public function testRegisterNew(): void
    {
        $url = '/api/register';
        $this->client->jsonRequest('POST', $url, [
            'login' => 'newuser',
            'password' => '1234',
            'email' => 'newuser@example.com',
            'firstname' => '$name',
            'lastname' => '$lastname',
        ]);

        self::assertResponseStatusCodeSame(201);
        self::assertJson(json_encode(null));

        // Registering with the same login should fail
        $this->client->jsonRequest('POST', $url, [
            'login' => 'newuser',
            'password' => '12345',
            'email' => 'newuser2@example.com',
            'firstname' => '$name',
            'lastname' => '$lastname',
        ]);

        self::assertResponseStatusCodeSame(400);
        $data = json_decode($this->client->getResponse()->getContent());
        self::assertTrue(isset($data->error));
    }

    public function testRegisterEmptyJson(): void
    {
        $url = '/api/register';

        $this->client->jsonRequest('POST', $url);

        self::assertResponseStatusCodeSame(400);
        $data = json_decode($this->client->getResponse()->getContent());
        self::assertTrue(isset($data->error));
    }

    public function testRegisterEmptyLogin(): void
    {
        $url = '/api/register';

        $this->client->jsonRequest('POST', $url, [
            'login' => '',
            'password' => '12345',
            'email' => 'newuser2@example.com',
            'firstname' => '$name',
            'lastname' => '$lastname',
        ]);

        self::assertResponseStatusCodeSame(400);
        $data = json_decode($this->client->getResponse()->getContent());
        self::assertTrue(isset($data->error));
    }

    public function testRegisterEmptyPassword(): void
    {
        $url = '/api/register';

        $this->client->jsonRequest('POST', $url, [
            'login' => 'bob',
            'password' => '',
            'email' => 'newuser2@example.com',
            'firstname' => '$name',
            'lastname' => '$lastname',
        ]);

        self::assertResponseStatusCodeSame(400);
        $data = json_decode($this->client->getResponse()->getContent());
        self::assertTrue(isset($data->error));
    }

    public function testRegisterEmptyEmail(): void
    {
        $url = '/api/register';

        $this->client->jsonRequest('POST', $url, [
            'login' => 'bob',
            'password' => 'fdheehdiu',
            'email' => '',
            'firstname' => '$name',
            'lastname' => '$lastname',
        ]);

        self::assertResponseStatusCodeSame(400);
        $data = json_decode($this->client->getResponse()->getContent());
        self::assertTrue(isset($data->error));
    }

    public function testRegisterInvalidEmail(): void
    {
        $url = '/api/register';

        $this->client->jsonRequest('POST', $url, [
            'login' => 'bob',
            'password' => 'fdheehdiu',
            'email' => 'notAnEmail',
            'firstname' => '$name',
            'lastname' => '$lastname',
        ]);

        self::assertResponseStatusCodeSame(400);
        $data = json_decode($this->client->getResponse()->getContent());
        self::assertTrue(isset($data->error));
    }

    public function testRegisterEmptyFirstname(): void
    {
        $url = '/api/register';

        $this->client->jsonRequest('POST', $url, [
            'login' => 'bob',
            'password' => 'fdheehdiu',
            'email' => 'bob@mail.org',
            'firstname' => '',
            'lastname' => '$lastname',
        ]);

        self::assertResponseStatusCodeSame(400);
        $data = json_decode($this->client->getResponse()->getContent());
        self::assertTrue(isset($data->error));
    }

    public function testRegisterEmptyLastname(): void
    {
        $url = '/api/register';

        $this->client->jsonRequest('POST', $url, [
            'login' => 'bob',
            'password' => 'fdheehdiu',
            'email' => 'bob@mail.org',
            'firstname' => 'Bob',
            'lastname' => '',
        ]);

        self::assertResponseStatusCodeSame(400);
        $data = json_decode($this->client->getResponse()->getContent());
        self::assertTrue(isset($data->error));
    }

    public function testLoginNormal(): void
    {
        $this->testRegisterNew();

        $url = '/api/login';

        $this->client->jsonRequest('POST', $url, [
            'login' => 'newuser',
            'password' => '1234',
        ]);

        self::assertResponseStatusCodeSame(200);
        $data = json_decode($this->client->getResponse()->getContent());
        self::assertTrue(isset($data->token));
        self::assertNotEmpty($data->token);
    }

    public function testLoginBadPassword(): void
    {
        $this->testRegisterNew();

        $url = '/api/login';

        $this->client->jsonRequest('POST', $url, [
            'login' => 'newuser',
            'password' => 'correcthorsestaple',
        ]);

        self::assertResponseStatusCodeSame(403);
        $data = json_decode($this->client->getResponse()->getContent());
        self::assertEquals('Invalid credentials', $data->error);
        self::assertTrue(isset($data->error));
    }

    public function testLoginBadLogin(): void
    {
        $this->testRegisterNew();

        $url = '/api/login';

        $this->client->jsonRequest('POST', $url, [
            'login' => 'baduser',
            'password' => '1234',
        ]);

        self::assertResponseStatusCodeSame(403);
        $data = json_decode($this->client->getResponse()->getContent());
        self::assertEquals('Invalid credentials', $data->error);
        self::assertTrue(isset($data->error));
    }

    public function testLoginEmptyJson(): void
    {
        $url = '/api/login';

        $this->client->jsonRequest('POST', $url);

        self::assertResponseStatusCodeSame(400);
        $data = json_decode($this->client->getResponse()->getContent());
        self::assertTrue(isset($data->error));
    }

    public function testLoginEmptyLogin(): void
    {
        $url = '/api/login';

        $this->client->jsonRequest('POST', $url, [
            'login' => '',
            'password' => '12345',
        ]);

        self::assertResponseStatusCodeSame(400);
        $data = json_decode($this->client->getResponse()->getContent());
        self::assertTrue(isset($data->error));
    }

    public function testLoginEmptyPassword(): void
    {
        $url = '/api/login';

        $this->client->jsonRequest('POST', $url, [
            'login' => 'user',
            'password' => '',
        ]);

        self::assertResponseStatusCodeSame(400);
        $data = json_decode($this->client->getResponse()->getContent());
        self::assertTrue(isset($data->error));
    }

    /**
     * @throws \Exception
     */
    public function testCurrentUserAuthenticated(): void
    {
        $url = '/api/users';

        $this->testLoginNormal();

        /** @var AccessTokenRepository $tokenRepository */
        $tokenRepository = static::getContainer()->get('doctrine')->getRepository(AccessToken::class);
        $token = $tokenRepository->findAll()[0];

        $this->client->jsonRequest('GET', $url, server: [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token->getValue(),
        ]);

        self::assertResponseStatusCodeSame(200);
        $data = json_decode($this->client->getResponse()->getContent());

        self::assertEquals('newuser', $data->login);
        self::assertEquals('newuser@example.com', $data->email);
        self::assertEquals('$name', $data->firstname);
        self::assertEquals('$lastname', $data->lastname);
        self::assertFalse(isset($data->password));
    }

    public function testCurrentBadToken(): void
    {
        $url = '/api/users';

        $this->testLoginNormal();

        $this->client->jsonRequest('GET', $url, server: [
            'HTTP_AUTHORIZATION' => 'Bearer LSkdfT99999999999999999999999999TlZ6sMJ5wVc',
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testCurrentMissingToken(): void
    {
        $url = '/api/users';

        $this->testLoginNormal();

        $this->client->jsonRequest('GET', $url);
        self::assertResponseStatusCodeSame(401);
    }
}
