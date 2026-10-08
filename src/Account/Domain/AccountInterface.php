<?php

declare(strict_types=1);

namespace Xver\PhpAuthCoreBundle\Account\Domain;

use Xver\PhpAppCoreBundle\Entity\Domain\EntityInterface;

interface AccountInterface extends EntityInterface
{
    /**
     * @psalm-return non-empty-string
     */
    public function getIdentifier(): string;

    /**
     * @psalm-return list<string> $roles
     */
    public function getRoles(): array;

    public function getPassword(): string;
}
