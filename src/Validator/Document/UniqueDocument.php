<?php declare(strict_types=1);

namespace Sofyco\Bundle\Doctrine\MongoDB\DocumentValidationBundle\Validator\Document;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class UniqueDocument extends Constraint
{
    public array $fields = [];
    public string $message = 'document.exists';
    public string $className;

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
