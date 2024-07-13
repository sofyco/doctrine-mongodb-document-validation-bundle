<?php declare(strict_types=1);

namespace Sofyco\Bundle\Doctrine\MongoDB\DocumentValidationBundle\Tests\Validator\Document;

use Doctrine\ODM\MongoDB\DocumentManager;
use Sofyco\Bundle\Doctrine\MongoDB\DocumentValidationBundle\Tests\App\Document\Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ExistsDocumentValidatorTest extends KernelTestCase
{
    public function testDocumentExists(): void
    {
        self::getDocumentManager()->getDocumentDatabase(Product::class)->drop();
        self::getDocumentManager()->persist(new Product(sku: '0000-0000', name: 'product-1'));
        self::getDocumentManager()->flush();

        $constraintViolationList = self::getValidator()->validate(new Product(sku: '0000-0000', name: 'product-2'));

        self::assertSame(0, $constraintViolationList->count());
    }

    public function testDocumentExistsWithStringFieldLikeNumber(): void
    {
        self::getDocumentManager()->getDocumentDatabase(Product::class)->drop();
        self::getDocumentManager()->persist(new Product(sku: '66226828401e131804016725', name: 'product-1'));
        self::getDocumentManager()->flush();

        $constraintViolationList = self::getValidator()->validate(new Product(sku: '66226828401e131804016725', name: 'product-2'));

        self::assertSame(0, $constraintViolationList->count());
    }

    public function testDocumentNotExists(): void
    {
        self::getDocumentManager()->getDocumentDatabase(Product::class)->drop();
        self::getDocumentManager()->persist(new Product(sku: '0000-0000', name: 'product-1'));
        self::getDocumentManager()->flush();

        $constraintViolationList = self::getValidator()->validate(new Product(sku: '0000-0001', name: 'product-2'));

        self::assertSame(1, $constraintViolationList->count());
        self::assertSame('validation.document.not_found', $constraintViolationList->get(0)->getMessage());
    }

    private static function getValidator(): ValidatorInterface
    {
        return self::getContainer()->get(ValidatorInterface::class); // @phpstan-ignore-line
    }

    private static function getDocumentManager(): DocumentManager
    {
        return self::getContainer()->get(DocumentManager::class); // @phpstan-ignore-line
    }
}
