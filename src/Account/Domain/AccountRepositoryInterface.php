<?php

declare(strict_types=1);

namespace Xver\PhpAuthCoreBundle\Account\Domain;

use Xver\PhpAppCoreBundle\Entity\Domain\EntityNotFoundException;
use Xver\PhpAppCoreBundle\Entity\Domain\EntityRepositoryInterface;

/**
 * @template-extends EntityRepositoryInterface<Account>
 */
interface AccountRepositoryInterface extends EntityRepositoryInterface
{
    public function findByIdentifier(string $identifier): ?Account;

    /**
     * @throws EntityNotFoundException
     */
    public function findByIdentifierOrThrowException(string $identifier): Account;
}
