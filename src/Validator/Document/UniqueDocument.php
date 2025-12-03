<?php declare(strict_types=1);

namespace Sofyco\Bundle\Doctrine\MongoDB\DocumentValidationBundle\Validator\Document;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class UniqueDocument extends Constraint
{
    /**
     * @param class-string $className
     * @param array<string, string>|array<int, string> $fields
     */
    public function __construct(public string $className, public array $fields = [], public string $message = 'document.exists')
    {
        parent::__construct();
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
