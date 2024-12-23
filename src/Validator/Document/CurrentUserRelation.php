<?php declare(strict_types=1);

namespace Sofyco\Bundle\Doctrine\MongoDB\DocumentValidationBundle\Validator\Document;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class CurrentUserRelation extends Constraint
{
    public array $roles = [];

    public string $message = 'user.invalid';

    /**
     * @var class-string
     */
    public string $className;

    public string $identifierProperty = 'id';

    public string $userProperty = 'user';
}
