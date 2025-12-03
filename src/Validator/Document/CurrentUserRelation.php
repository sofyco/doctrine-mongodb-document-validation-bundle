<?php declare(strict_types=1);

namespace Sofyco\Bundle\Doctrine\MongoDB\DocumentValidationBundle\Validator\Document;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class CurrentUserRelation extends Constraint
{
    /**
     * @param class-string $className
     */
    public function __construct(public string $className, public array $roles = [], public string $identifierProperty = 'id', public string $userProperty = 'user', public string $message = 'user.invalid')
    {
        parent::__construct();
    }
}
