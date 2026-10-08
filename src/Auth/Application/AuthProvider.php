<?php

declare(strict_types=1);

namespace Xver\PhpAuthCoreBundle\Auth\Application;

use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Xver\PhpAppCoreBundle\Entity\Domain\EntityNotFoundException;
use Xver\PhpAuthCoreBundle\Account\Application\Query\AccountQueryInterface;
use Xver\PhpAuthCoreBundle\Auth\Domain\AuthUser;

/**
 * @template-implements UserProviderInterface<UserInterface>
 *
 * @api
 */
class AuthProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    public function __construct(
        protected readonly AccountQueryInterface $accountQuery
    ) {}

    /**
     * @throws UserNotFoundException          if the user is not found
     * @throws AuthenticationServiceException else
     */
    #[\Override]
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        try {
            $account = $this->accountQuery->findByIdentifierOrThrowException($identifier);

            return new AuthUser(
                $account->getIdentifier(),
                $account->getRoles(),
                $account->getPassword()
            );
        } catch (\Throwable $th) {
            if ($th instanceof EntityNotFoundException) {
                throw new UserNotFoundException();
            }

            throw new AuthenticationServiceException();
        }
    }

    /**
     * Refreshes the user after being reloaded from the session.
     *
     * When a user is logged in, at the beginning of each request, the
     * User object is loaded from the session and then this method is
     * called. Your job is to make sure the user's data is still fresh by,
     * for example, re-querying for fresh User data.
     *
     * If your firewall is "stateless: true" (for a pure API), this
     * method is not called.
     *
     * @throws UnsupportedUserException
     */
    #[\Override]
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof AuthUser) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', $user::class));
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    /**
     * Tells Symfony to use this provider for this User class.
     */
    #[\Override]
    public function supportsClass(string $class): bool
    {
        return AuthUser::class === $class || is_subclass_of($class, AuthUser::class);
    }

    /**
     * No user password upgrades scoped.
     */
    #[\Override]
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void {}
}
