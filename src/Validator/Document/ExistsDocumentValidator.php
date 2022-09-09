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
            ->setParameter('{{ values }}', \implode(', ', $criteria))
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
                $criteria[$field] = \is_numeric($value) ? (int) $value : $value;
            }
        }

        return $criteria;
    }

    private function isExistsDocument(string $documentName, array $criteria): bool
    {
        $queryBuilder = $this->dm->createQueryBuilder($documentName)->count();

        foreach ($criteria as $field => $value) {
            $queryBuilder->field($field)->equals($value);
        }

        return (bool) $queryBuilder->getQuery()->execute();
    }
}
