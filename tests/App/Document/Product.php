<?php declare(strict_types=1);

namespace Sofyco\Bundle\Doctrine\MongoDB\DocumentValidationBundle\Tests\App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Sofyco\Bundle\Doctrine\MongoDB\DocumentValidationBundle\Validator\Document\ExistsDocument;
use Sofyco\Bundle\Doctrine\MongoDB\DocumentValidationBundle\Validator\Document\UniqueDocument;

#[MongoDB\Document(collection: 'products')]
#[ExistsDocument(['fields' => ['sku'], 'className' => Product::class])]
#[UniqueDocument(['fields' => ['name'], 'className' => Product::class])]
final class Product
{
    #[MongoDB\Id]
    public string $id;

    #[MongoDB\Field]
    public string $sku;

    #[MongoDB\Field]
    public string $name;

    public function __construct(string $sku, string $name)
    {
        $this->sku = $sku;
        $this->name = $name;
    }
}
