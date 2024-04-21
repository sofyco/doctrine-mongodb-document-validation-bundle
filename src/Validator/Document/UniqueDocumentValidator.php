<?php declare(strict_types=1);

namespace Sofyco\Bundle\Doctrine\MongoDB\DocumentValidationBundle\Validator\Document;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception;

final class UniqueDocumentValidator extends ConstraintValidator
{
    public function __construct(private readonly DocumentManager $dm)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueDocument) {
            throw new Exception\UnexpectedTypeException($constraint, UniqueDocument::class);
        }

        if (!\is_object($value)) {
            return;
        }

        $criteria = $this->getCriteria($value, $constraint);

        if (!$criteria) {
            return;
        }

        if (isset($criteria['id'])) {
            if (\is_object($criteria['id']) && \method_exists($criteria['id'], 'getId')) {
                $sourceId = $criteria['id']->getId();
            } else {
                $sourceId = $criteria['id'];
            }

            unset($criteria['id']);
        } else {
            $sourceId = null;
        }

        $id = $this->fetchDocumentId($constraint->className, $criteria);

        if (null === $id || $sourceId === $id) {
            return;
        }

        $this->context
            ->buildViolation($constraint->message)
            ->atPath(\strval(\array_key_first($criteria)))
            ->setParameter('{{ values }}', $this->getViolationParameters($criteria))
            ->addViolation();
    }

    private function getCriteria(object $dto, UniqueDocument $constraint): array
    {
        $criteria = [];

        foreach ($constraint->fields as $property => $field) {
            if (\is_string($property)) {
                $field = $property;
                $value = $dto->{$property} ?? $property;
            } else {
                $value = $dto->{$field} ?? null;
            }

            $criteria[$field] = \preg_match('#^\d+$#', (string) $value) ? (int) $value : $value;
        }

        return $criteria;
    }

    private function fetchDocumentId(string $documentName, array $criteria): ?string
    {
        $queryBuilder = $this->dm->createQueryBuilder($documentName);

        foreach ($criteria as $field => $value) {
            $queryBuilder->field($field)->equals($value);
        }

        $document = $queryBuilder->getQuery()->getSingleResult();

        return \is_object($document) ? $this->getDocumentId($document) : null;
    }

    private function getViolationParameters(array $criteria): string
    {
        return \implode(', ', \array_map(fn($document) => $this->getDocumentId($document), $criteria));
    }

    private function getDocumentId(mixed $document): ?string
    {
        if (\is_string($document)) {
            return $document;
        }

        if (\is_object($document) && isset($document->id)) {
            return $document->id;
        }

        if (\is_object($document) && \method_exists($document, 'getId')) {
            return $document->getId();
        }

        return null;
    }
}
