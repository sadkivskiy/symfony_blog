<?php

namespace App\Doctrine;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;

/**
 * Class ArticleSetOwnerListener
 * @package App\Doctrine
 */
class ArticleSetOwnerListener
{
    /**
     * @var Security
     */
    private $security;

    /**
     * ArticleSetOwnerListener constructor.
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @param Article $article
     */
    public function prePersist(Article $article)
    {
        if ($article->getOwner()) {
            return;
        }

        /** @var User $user */
        $user = $this->security->getUser();

        if ($user) {
            $article->setOwner($user);
        }
    }
}
