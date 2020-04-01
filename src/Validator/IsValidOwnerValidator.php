<?php

namespace App\Validator;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class IsValidOwnerValidator
 * @package App\Validator
 */
class IsValidOwnerValidator extends ConstraintValidator
{
    /**
     * @var Security
     */
    private $security;

    /**
     * IsValidOwnerValidator constructor.
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint IsValidOwner */

        if (null === $value || '' === $value) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            $this->context->buildViolation($constraint->anonymousMessage)->addViolation();

            return;
        }

        if (!$value instanceof User) {
            throw new \InvalidArgumentException('@IsValidOwner constraint must be put on a property containing a User object');
        }

        // allow admin users to change owners
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        if ($value->getId() !== $user->getId()) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
