<?php

declare(strict_types=1);

namespace Xver\PhpAuthCoreBundle\Account\Domain;

use Xver\PhpAppCoreBundle\Entity\Domain\EntityPersistenceInterface;
use Xver\PhpAppCoreBundle\Entity\Domain\EntityRepositoryInterface;

/**
 * @template-extends EntityPersistenceInterface<Account>
 */
interface AccountPersistenceInterface extends EntityPersistenceInterface
{
    /**
     * @return AccountRepositoryInterface
     */
    #[\Override]
    public function getRepository(): EntityRepositoryInterface;
}
