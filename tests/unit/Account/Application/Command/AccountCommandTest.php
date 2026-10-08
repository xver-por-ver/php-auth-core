<?php

declare(strict_types=1);

namespace Xver\PhpAuthCoreBundle\Tests\unit\Account\Application\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Xver\PhpAppCoreBundle\Exception\Domain\DomainViolationException;
use Xver\PhpAuthCoreBundle\Account\Application\Command\AccountCommand;
use Xver\PhpAuthCoreBundle\Account\Domain\Account;
use Xver\PhpAuthCoreBundle\Account\Domain\AccountPersistenceInterface;
use Xver\PhpAuthCoreBundle\Account\Domain\AccountRepositoryInterface;

/**
 * @internal
 */
#[CoversClass(AccountCommand::class)]
#[UsesClass(Account::class)]
class AccountCommandTest extends TestCase
{
    private static string $email;
    private static string $password;
    private static array $roles;
    private AccountPersistenceInterface&Stub $accountPersistence;

    public static function setUpBeforeClass(): void
    {
        self::$email = 'test@example.com';
        self::$password = 'password';
        self::$roles = ['ROLE_USER'];
    }

    public function setUp(): void
    {
        $this->accountPersistence = $this->createStub(AccountPersistenceInterface::class);
    }

    public function testCreateCommandExecutionSuccessfuly(): void
    {
        $this->accountPersistence->method('getRepository')->willReturn($this->createStub(AccountRepositoryInterface::class));
        $command = new AccountCommand($this->accountPersistence);
        $account = $command->create(
            self::$email,
            self::$password,
            self::$roles
        );
        $this->assertInstanceOf(Account::class, $account);
    }

    public function testCreateCommandBadNewAccountThrowsException(): void
    {
        $command = new AccountCommand($this->accountPersistence);
        $this->expectException(DomainViolationException::class);
        $this->expectExceptionMessageIs('invalidEmail');
        $command->create(
            'invalidemail',
            self::$password,
            self::$roles
        );
    }
}
