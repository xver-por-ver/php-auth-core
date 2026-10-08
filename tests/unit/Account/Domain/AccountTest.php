<?php

declare(strict_types=1);

namespace Xver\PhpAuthCoreBundle\Tests\unit\Account\Domain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;
use Xver\PhpAppCoreBundle\Entity\Domain\EntityInterface;
use Xver\PhpAppCoreBundle\Exception\Domain\DomainViolationException;
use Xver\PhpAuthCoreBundle\Account\Domain\Account;
use Xver\PhpAuthCoreBundle\Account\Domain\AccountPersistenceInterface;
use Xver\PhpAuthCoreBundle\Account\Domain\AccountRepositoryInterface;

/**
 * @internal
 */
#[CoversClass(Account::class)]
class AccountTest extends TestCase
{
    private AccountRepositoryInterface&Stub $repoAccount;
    private AccountPersistenceInterface&Stub $accountPersistence;

    public function setUp(): void
    {
        $this->repoAccount = $this->createStub(AccountRepositoryInterface::class);
        $this->accountPersistence = $this->createStub(AccountPersistenceInterface::class);
        $this->accountPersistence->method('getRepository')->willReturn($this->repoAccount);
    }

    public function testAccountObjectIsCreated(): void
    {
        $email = 'test@example.com';
        $password = 'password';
        $account = new Account($this->accountPersistence, $email, $password, ['ROLE_ADMIN']);
        $this->assertInstanceOf(Uuid::class, $account->getId());
        $this->assertTrue($account->sameId($account));
        $this->assertCount(2, $account->getRoles());
        $this->assertContains('ROLE_USER', $account->getRoles());
        $this->assertContains('ROLE_ADMIN', $account->getRoles());
        $this->assertSame($email, $account->getEmail());
        $this->assertSame($password, $account->getPassword());
        $this->assertSame($email, $account->getIdentifier());
    }

    #[DataProvider('invalidEmails')]
    public function testCreateAccountWithInvalidEmailThrowsException($email): void
    {
        $this->expectException(DomainViolationException::class);
        $this->expectExceptionMessage('invalidEmail');
        new Account($this->accountPersistence, $email, 'password', ['ROLE_USER']);
    }

    public static function invalidEmails(): array
    {
        return [
            [''],
            ['test'],
            ['test@example'],
            ['@example.com'],
            ['test@'],
        ];
    }

    public function testCreateAccountWithInvalidRoleThrowsException(): void
    {
        $this->expectException(DomainViolationException::class);
        $this->expectExceptionMessage('invalidUserRole');
        new Account($this->accountPersistence, 'test@example.com', 'password', ['ROLE_NOEXISTS']);
    }

    public function testSameIdWithInvalidEntityThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $account = new Account($this->accountPersistence, 'test@example.com', 'password', ['ROLE_USER']);
        $entity = new class implements EntityInterface {
            public function sameId(EntityInterface $otherEntity): bool
            {
                return true;
            }
        };
        $account->sameId($entity);
    }

    #[DataProvider('invalidPasswords')]
    public function testCreateAccountWithInvalidPasswordThrowsException($password): void
    {
        $this->expectException(DomainViolationException::class);
        $this->expectExceptionMessage('minPasswordLength');
        new Account($this->accountPersistence, 'test@example.com', $password, ['ROLE_USER']);
    }

    public static function invalidPasswords(): array
    {
        return [
            [''],
            ['1234567'],
        ];
    }

    public function testCreateAccountWithExistingEmailThrowsException(): void
    {
        $account = $this->createStub(Account::class);
        $this->repoAccount->method('findByIdentifier')->willReturn($account);
        $this->accountPersistence->method('getRepository')->willReturn($this->repoAccount);
        $this->expectException(DomainViolationException::class);
        $this->expectExceptionMessage('accountEmailExists');
        $account = new Account($this->accountPersistence, 'test@example.com', '12345678', ['ROLE_ADMIN']);
    }
}
