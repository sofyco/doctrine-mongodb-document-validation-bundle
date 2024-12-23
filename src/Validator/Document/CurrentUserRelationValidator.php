<?php declare(strict_types=1);

namespace Sofyco\Bundle\Doctrine\MongoDB\DocumentValidationBundle\Validator\Document;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception;

final class CurrentUserRelationValidator extends ConstraintValidator
{
    public function __construct(private readonly Security $security, private readonly DocumentManager $dm)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CurrentUserRelation) {
            throw new Exception\UnexpectedTypeException($constraint, CurrentUserRelation::class);
        }

        if (empty($value)) {
            return;
        }

        if (null === $user = $this->security->getUser()) {
            return;
        }

        $identifiers = (array) $value;

        $count = $this->dm
            ->createQueryBuilder($constraint->className)
            ->count()
            ->field($constraint->identifierProperty)->in($identifiers)
            ->field($constraint->userProperty)->equals($user->getUserIdentifier())
            ->getQuery()
            ->execute();

        if ($count === count($identifiers)) {
            return;
        }

        foreach ($constraint->roles as $role) {
            if ($this->security->isGranted($role)) {
                return;
            }
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }
}
