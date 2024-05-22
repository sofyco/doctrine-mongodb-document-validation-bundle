<?php declare(strict_types=1);

namespace Sofyco\Bundle\Doctrine\MongoDB\DocumentValidationBundle\Validator\Document;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception;

final class ExistsDocumentValidator extends ConstraintValidator
{
    public function __construct(private readonly DocumentManager $dm)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistsDocument) {
            throw new Exception\UnexpectedTypeException($constraint, ExistsDocument::class);
        }

        if (!\is_object($value)) {
            return;
        }

        $criteria = $this->getCriteria($value, $constraint);

        if (!$criteria) {
            return;
        }

        if ($this->isExistsDocument($constraint->className, $criteria)) {
            return;
        }

        $this->context
            ->buildViolation($constraint->message)
            ->atPath(\strval(\array_key_first($criteria)))
            ->setParameter('{{ values }}', \json_encode($criteria, \JSON_THROW_ON_ERROR))
            ->addViolation();
    }

    private function getCriteria(object $dto, ExistsDocument $constraint): array
    {
        $criteria = [];

        foreach ($constraint->fields as $property => $field) {
            if (\is_numeric($property)) {
                $property = $field;
            }

            if ($value = $dto->{$property}) {
                if (\is_string($value) && \preg_match('#^\d+$#', $value) && (int) $value !== \PHP_INT_MAX) {
                    $criteria[$field] = (int) $value;
                } else {
                    $criteria[$field] = $value;
                }
            }
        }

        return $criteria;
    }

    private function isExistsDocument(string $documentName, array $criteria): bool
    {
        $queryBuilder = $this->dm->createQueryBuilder($documentName)->count();
        $expectedCount = 1;

        foreach ($criteria as $field => $value) {
            if (\is_array($value)) {
                $expectedCount = \count($value);

                $queryBuilder->field($field)->in($value);
            } else {
                $queryBuilder->field($field)->equals($value);
            }
        }

        return $expectedCount === $queryBuilder->getQuery()->execute();
    }
}
