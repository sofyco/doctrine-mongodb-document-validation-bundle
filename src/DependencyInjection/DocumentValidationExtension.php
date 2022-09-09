<?php declare(strict_types=1);

namespace Sofyco\Bundle\Doctrine\MongoDB\DocumentValidationBundle\DependencyInjection;

use Sofyco\Bundle\Doctrine\MongoDB\DocumentValidationBundle\Validator\Document\ExistsDocumentValidator;
use Sofyco\Bundle\Doctrine\MongoDB\DocumentValidationBundle\Validator\Document\UniqueDocumentValidator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class DocumentValidationExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $existsDocumentValidator = new Definition(ExistsDocumentValidator::class);
        $existsDocumentValidator->setAutowired(true);
        $existsDocumentValidator->addTag('validator.constraint_validator');
        $container->setDefinition(ExistsDocumentValidator::class, $existsDocumentValidator);

        $uniqueDocumentValidator = new Definition(UniqueDocumentValidator::class);
        $uniqueDocumentValidator->setAutowired(true);
        $uniqueDocumentValidator->addTag('validator.constraint_validator');
        $container->setDefinition(UniqueDocumentValidator::class, $uniqueDocumentValidator);
    }
}
