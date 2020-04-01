<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Article;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class ArticleDataPersister
 * @package App\DataPersister
 */
class ArticleDataPersister implements DataPersisterInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * UserDataPersister constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $data
     * @return bool
     */
    public function supports($data): bool
    {
        return $data instanceof Article;
    }

    /**
     * @param Article $data
     * @throws Exception
     */
    public function persist($data)
    {
        $data->setUpdatedAt(new DateTimeImmutable());
        if ($data->getCreatedAt() === null) {
            $data->setCreatedAt(new DateTimeImmutable());
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    /**
     * @param $data
     */
    public function remove($data)
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}
