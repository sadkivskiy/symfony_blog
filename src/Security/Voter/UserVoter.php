<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserVoter
 * @package App\Security\Voter
 */
class UserVoter extends Voter
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
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['VIEW', 'VIEW_LIST', 'EDIT', 'DELETE']) && $subject instanceof User;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var User $subject */

        $user = $token->getUser();

        // if the user is anonymous, can only create (register)
        if (!$user instanceof UserInterface) {
            return $attribute === 'CREATE';
        }

        // Admin has full access
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Only admin can view user lists
        if ($attribute === 'VIEW_LIST') {
            return false;
        }

        // If User is disabled false
        if ($subject->getStatus() === User::STATUS_DISABLED) {
            return false;
        }

        return $subject === $user;
    }
}
