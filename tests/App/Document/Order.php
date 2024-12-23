<?php declare(strict_types=1);

namespace Sofyco\Bundle\Doctrine\MongoDB\DocumentValidationBundle\Tests\App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Sofyco\Bundle\Doctrine\MongoDB\DocumentValidationBundle\Validator\Document\CurrentUserRelation;

#[MongoDB\Document(collection: 'orders')]
final class Order
{
    #[MongoDB\Id]
    public string $id;

    #[MongoDB\ReferenceOne(storeAs: 'id', targetDocument: User::class)]
    #[CurrentUserRelation(['className' => User::class])]
    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
