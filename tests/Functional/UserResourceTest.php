<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class UserResourceTest
 * @package App\Tests\Functional
 */
class UserResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    /**
     * @throws TransportExceptionInterface
     */
    public function testCreateUser()
    {
        $client = self::createClient();

        $client->request('POST', '/api/users', [
            'json' => [
                'email' => 'webvemon@i.ua',
                'firstName' => 'First',
                'lastName' => 'Last',
                'password' => 'Pass13'
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);

        $this->logIn($client, 'webvemon@i.ua', 'Pass13');
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testUpdateUser()
    {
        $client = self::createClient();
        $user = $this->createUserAndLogIn($client, 'webvemon@example.com', 'foo', [
            'firstName' => 'First',
            'lastName' => 'Last',
        ]);

        $client->request('PUT', '/api/users/'.$user->getId(), [
            'json' => [
                'firstName' => 'First',
                'lastName' => 'Last',
                'roles' => ['ROLE_ADMIN'] // will be ignored
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $data = $client->getResponse()->toArray();
        $this->assertEquals($data['firstName'], 'First');
        $this->assertEquals($data['lastName'], 'Last');

        $em = $this->getEntityManager();
        /** @var User $user */
        $user = $em->getRepository(User::class)->find($user->getId());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testGetUser()
    {
        $client = self::createClient();
        $user = $this->createUser('webvemon@example.com', 'foo', [
            'firstName' => 'First',
            'lastName' => 'Last',
        ]);
        $user2 = $this->createUserAndLogIn($client, 'authenticated@example.com', 'foo', [
            'firstName' => 'First2',
            'lastName' => 'Last2',
        ]);

        $user->setPhoneNumber('555.123.4567');
        $em = $this->getEntityManager();
        $em->flush();

        $client->request('GET', '/api/users/'.$user->getId());
        $this->assertResponseStatusCodeSame(403);

        $client->request('GET', '/api/users/'.$user2->getId());
        $data = $client->getResponse()->toArray();
        $this->assertEquals($data['firstName'], 'First2');
        $this->assertEquals($data['lastName'], 'Last2');
        $this->assertEquals($data['phoneNumber'], null);

        // refresh the user & elevate
        $user = $em->getRepository(User::class)->find($user->getId());
        $user->setRoles(['ROLE_ADMIN']);
        $em->flush();
        $this->logIn($client, $user->getEmail(), 'foo');

        $client->request('GET', '/api/users/'.$user->getId());
        $data = $client->getResponse()->toArray();
        $this->assertEquals($data['phoneNumber'], '555.123.4567');
    }
}