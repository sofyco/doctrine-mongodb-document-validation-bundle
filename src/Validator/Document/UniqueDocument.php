<?php declare(strict_types=1);

namespace Sofyco\Bundle\Doctrine\MongoDB\DocumentValidationBundle\Validator\Document;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class UniqueDocument extends Constraint
{
    public array $fields = [];
    public string $message = 'validation.document.exists';
    public string $className;

    public function getTargets(): array
    {
        return [self::CLASS_CONSTRAINT];
    }
}
