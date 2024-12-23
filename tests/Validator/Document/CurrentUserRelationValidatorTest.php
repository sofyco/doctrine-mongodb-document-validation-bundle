<?php declare(strict_types=1);

namespace Sofyco\Bundle\Doctrine\MongoDB\DocumentValidationBundle\Tests\Validator\Document;

use Doctrine\ODM\MongoDB\DocumentManager;
use Sofyco\Bundle\Doctrine\MongoDB\DocumentValidationBundle\Tests\App\Document\Order;
use Sofyco\Bundle\Doctrine\MongoDB\DocumentValidationBundle\Tests\App\Document\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CurrentUserRelationValidatorTest extends KernelTestCase
{
    public function testUnauthorizedUser(): void
    {
        $constraintViolationList = self::getValidator()->validate(new Order(user: new User()));

        self::assertSame(0, $constraintViolationList->count());
    }

    private static function getValidator(): ValidatorInterface
    {
        return self::getContainer()->get(ValidatorInterface::class); // @phpstan-ignore-line
    }
}
