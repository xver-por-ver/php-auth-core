<?php

declare(strict_types=1);

namespace Xver\PhpAuthCoreBundle\Account\Application\Query;

use Xver\PhpAuthCoreBundle\Account\Domain\AccountInterface;

interface AccountQueryInterface
{
    public function findByIdentifierOrThrowException(string $identifier): AccountInterface;
}
