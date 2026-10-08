<?php

declare(strict_types=1);

namespace Xver\PhpAuthCoreBundle\Account\Domain;

use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Uid\Uuid;
use Xver\PhpAppCoreBundle\Entity\Domain\EntityInterface;
use Xver\PhpAppCoreBundle\Exception\Domain\DomainViolationException;

/**
 * @api
 */
class Account implements AccountInterface
{
    public const array AVAILABLE_ROLES = ['ROLE_ADMIN', 'ROLE_USER'];
    public const int PASSWORD_MIN_LENGTH = 8;

    protected Uuid $id;

    /**
     * @psalm-param non-empty-string $email
     * @psalm-param list<string> $roles
     */
    public function __construct(
        private readonly AccountPersistenceInterface $accountPersistence,
        protected string $email,
        protected string $password,
        protected array $roles = ['ROLE_USER']
    ) {
        $this->id = Uuid::v4();
        $this
            ->validEmail()
            ->validRoles()
            ->validPassword()
        ;
        $this->persistCreate();
    }

    /**
     * @psalm-return non-empty-string
     */
    #[\Override]
    public function getIdentifier(): string
    {
        return $this->getEmail();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    #[\Override]
    public function sameId(EntityInterface $otherEntity): bool
    {
        if (!$otherEntity instanceof Account) {
            throw new \InvalidArgumentException();
        }

        return $this->getId()->equals($otherEntity->getId());
    }

    /**
     * @psalm-return non-empty-string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    private function validEmail(): self
    {
        if (
            false === filter_var($this->email, FILTER_VALIDATE_EMAIL)
            || empty($this->email)
        ) {
            throw new DomainViolationException(
                new TranslatableMessage(
                    'invalidEmail',
                    [],
                    'PhpAuthCoreBundle'
                ),
                'account.email'
            );
        }

        return $this;
    }

    /**
     * @psalm-return list<string> $roles
     */
    #[\Override]
    public function getRoles(): array
    {
        return $this->roles;
    }

    private function validRoles(): self
    {
        foreach ($this->roles as $role) {
            if (!in_array($role, self::AVAILABLE_ROLES)) {
                throw new DomainViolationException(
                    new TranslatableMessage(
                        'invalidUserRole',
                        [],
                        'PhpAuthCoreBundle'
                    ),
                    'account.role'
                );
            }
        }
        // guarantee every user at least has ROLE_USER
        if (!in_array('ROLE_USER', $this->roles)) {
            array_push($this->roles, 'ROLE_USER');
        }

        return $this;
    }

    #[\Override]
    public function getPassword(): string
    {
        return $this->password;
    }

    public function validPassword(): self
    {
        if (mb_strlen($this->password) < self::PASSWORD_MIN_LENGTH) {
            throw new DomainViolationException(
                new TranslatableMessage(
                    'minPasswordLength',
                    ['limit' => self::PASSWORD_MIN_LENGTH],
                    'PhpAuthCoreBundle'
                ),
                'account.password'
            );
        }

        return $this;
    }

    protected function persistCreate(): void
    {
        $repoAccount = $this->accountPersistence->getRepository();
        $this->validateIdentifierUniqueness($repoAccount);
        $this->accountPersistence->persist($this);
        $this->accountPersistence->flush();
    }

    protected function validateIdentifierUniqueness(AccountRepositoryInterface $repoAccount): void
    {
        if (null !== $repoAccount->findByIdentifier($this->getEmail())) {
            throw new DomainViolationException(
                new TranslatableMessage(
                    'accountEmailExists',
                    [],
                    'PhpAuthCoreBundle'
                ),
                'account.email'
            );
        }
    }
}
