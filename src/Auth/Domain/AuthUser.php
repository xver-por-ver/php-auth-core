<?php

declare(strict_types=1);

namespace Xver\PhpAuthCoreBundle\Auth\Domain;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class AuthUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @psalm-param non-empty-string $identifier
     * @psalm-param array<string> $roles
     */
    public function __construct(
        private readonly string $identifier,
        private readonly array $roles,
        private readonly string $password
    ) {}

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    #[\Override]
    public function getUserIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return array<string>
     */
    #[\Override]
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    #[\Override]
    public function getPassword(): string
    {
        return $this->password;
    }
}
