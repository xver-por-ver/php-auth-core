<?php

declare(strict_types=1);

namespace Xver\PhpAuthCoreBundle\Account\Application\Command;

use Xver\PhpAuthCoreBundle\Account\Domain\Account;
use Xver\PhpAuthCoreBundle\Account\Domain\AccountPersistenceInterface;

/**
 * @api
 */
class AccountCommand
{
    public function __construct(private AccountPersistenceInterface $accountPersistence) {}

    /**
     * @psalm-param non-empty-string $email
     * @psalm-param non-empty-string $password
     * @psalm-param list<string> $roles
     */
    public function create(
        string $email,
        string $password,
        array $roles
    ): Account {
        return new Account(
            $this->accountPersistence,
            $email,
            $password,
            $roles
        );
    }
}
