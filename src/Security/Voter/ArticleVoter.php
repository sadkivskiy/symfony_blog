<?php

namespace App\Security\Voter;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class PostVoter
 * @package App\Security\Voter
 */
class ArticleVoter extends Voter
{
    /**
     * @var Security
     */
    private $security;

    /**
     * CheeseListingVoter constructor.
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['VIEW', 'EDIT', 'DELETE']) && $subject instanceof Article;
    }

    /**
     * @param string $attribute
     * @param mixed|Article $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Admin has full access
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // If User is disabled false
        if ($subject->status === User::STATUS_DISABLED) {
            return false;
        }

        return $subject === $user;
    }
}
