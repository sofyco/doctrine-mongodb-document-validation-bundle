<?php declare(strict_types=1);

namespace Sofyco\Bundle\Doctrine\MongoDB\DocumentValidationBundle\Validator\Document;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class ExistsDocument extends Constraint
{
    public array $fields = [];
    public string $message = 'validation.document.not_found';
    public string $className;

    public function getTargets(): array
    {
        return [self::CLASS_CONSTRAINT];
    }
}
