<?php

declare(strict_types=1);

namespace Xver\PhpAuthCoreBundle\Tests\unit\Auth\Application;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Xver\PhpAppCoreBundle\Entity\Domain\EntityNotFoundException;
use Xver\PhpAuthCoreBundle\Account\Application\Query\AccountQueryInterface;
use Xver\PhpAuthCoreBundle\Account\Domain\Account;
use Xver\PhpAuthCoreBundle\Auth\Application\AuthProvider;
use Xver\PhpAuthCoreBundle\Auth\Domain\AuthUser;

/**
 * @internal
 */
#[CoversClass(AuthProvider::class)]
#[UsesClass(AuthUser::class)]
#[AllowMockObjectsWithoutExpectations]
class AuthProviderTest extends TestCase
{
    public function testLoadUserByIdentifierThrowsUserNotFoundException(): void
    {
        $accountQuery = $this->getMockBuilder(AccountQueryInterface::class)->getMock();
        $accountQuery->expects($this->once())->method('findByIdentifierOrThrowException')->willThrowException(
            new EntityNotFoundException('User', '1')
        );
        $provider = new AuthProvider($accountQuery);
        $this->expectException(UserNotFoundException::class);
        $provider->loadUserByIdentifier('test@example.com');
    }

    public function testLoadUserByIdentifierThrowsServiceException(): void
    {
        $accountQuery = $this->getMockBuilder(AccountQueryInterface::class)->getMock();
        $accountQuery->expects($this->once())->method('findByIdentifierOrThrowException')->willThrowException(new \Exception('whatever'));
        $this->expectException(AuthenticationServiceException::class);
        $provider = new AuthProvider($accountQuery);
        $provider->loadUserByIdentifier('test@example.com');
    }

    public function testUserFound(): void
    {
        $account = $this->getMockBuilder(Account::class)->disableOriginalConstructor()->getMock();
        $accountQuery = $this->getMockBuilder(AccountQueryInterface::class)->getMock();
        $accountQuery->expects($this->once())->method('findByIdentifierOrThrowException')->willReturn($account);
        $provider = new AuthProvider($accountQuery);
        $this->assertInstanceOf(AuthUser::class, $provider->loadUserByIdentifier('test@example.com'));
    }

    public function testRefreshUserNotFoundThrowsException(): void
    {
        $accountQuery = $this->getMockBuilder(AccountQueryInterface::class)->getMock();
        $authUser = $this->getMockBuilder(UserInterface::class)->getMock();
        $provider = new AuthProvider($accountQuery);
        $this->expectException(UnsupportedUserException::class);
        $provider->refreshUser($authUser);
    }

    public function testRefreshUserWithFound(): void
    {
        $account = $this->getMockBuilder(Account::class)->disableOriginalConstructor()->getMock();
        $accountQuery = $this->getMockBuilder(AccountQueryInterface::class)->getMock();
        $accountQuery->expects($this->once())->method('findByIdentifierOrThrowException')->willReturn($account);
        $provider = new AuthProvider($accountQuery);
        $authUser = new AuthUser('', [], '');
        $this->assertInstanceOf(AuthUser::class, $provider->refreshUser($authUser));
    }

    public function testSupportsClass(): void
    {
        $accountQuery = $this->getMockBuilder(AccountQueryInterface::class)->getMock();
        $provider = new AuthProvider($accountQuery);
        $this->assertTrue($provider->supportsClass(AuthUser::class));
    }

    public function testUpgradePassword(): void
    {
        $accountQuery = $this->getMockBuilder(AccountQueryInterface::class)->getMock();
        $provider = new AuthProvider($accountQuery);
        $provider->upgradePassword(new AuthUser('', [], ''), '');
        $this->assertTrue(true);
    }
}
