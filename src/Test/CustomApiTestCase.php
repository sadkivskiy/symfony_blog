<?php

namespace App\Test;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class CustomApiTestCase
 * @package App\Test
 */
class CustomApiTestCase extends ApiTestCase
{
    /**
     * @param string $email
     * @param string $password
     * @param array $data
     * @return User
     */
    protected function createUser(string $email, string $password, array $data): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);

        $encoded = self::$container->get('security.password_encoder')
            ->encodePassword($user, $password);
        $user->setPassword($encoded);

        $em = self::$container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * @param Client $client
     * @param string $email
     * @param string $password
     * @throws TransportExceptionInterface
     */
    protected function logIn(Client $client, string $email, string $password)
    {
        $client->request('POST', '/login', [
            'json' => [
                'email' => $email,
                'password' => $password
            ],
        ]);
        $this->assertResponseStatusCodeSame(204);
    }

    /**
     * @param Client $client
     * @param string $email
     * @param string $password
     * @param array $data
     * @return User
     * @throws TransportExceptionInterface
     */
    protected function createUserAndLogIn(Client $client, string $email, string $password, array $data): User
    {
        $user = $this->createUser($email, $password, $data);

        $this->logIn($client, $email, $password);

        return $user;
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager(): EntityManagerInterface
    {
        return self::$container->get('doctrine')->getManager();
    }
}