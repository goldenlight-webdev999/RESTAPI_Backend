<?php

declare(strict_types=1);


namespace App\Domain\User;

use DDD\Embeddable\EmailAddress;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Infrastructure\Normalization\ObjectNormalizer as Scope;

/**
 * Class User
 * @package App\Domain\User
 */
class User
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_USER_BASIC = 'ROLE_USER_BASIC'; // Basic user
    public const ROLE_USER_PRO = 'ROLE_USER_PRO'; // Pro user
    public const ROLE_USER_BUSINESS = 'ROLE_USER_BUSINESS'; // Business user
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @Groups({Scope::SCOPE_PRIVATE, Scope::SCOPE_ADMIN})
     */
    protected $id;
    /**
     * @Groups({Scope::SCOPE_PRIVATE, Scope::SCOPE_ADMIN})
     */
    protected $email;
    /**
     * @Groups({Scope::SCOPE_PRIVATE, Scope::SCOPE_ADMIN})
     */
    protected $userName;
    protected $password;
    /**
     * @Groups({Scope::SCOPE_ADMIN})
     */
    protected $acceptsCommercialCommunications;
    /**
     * @Groups({Scope::SCOPE_ADMIN})
     */
    protected $roles = [];

    protected $subscriptions;

    /**
     * @Groups({Scope::SCOPE_PRIVATE, Scope::SCOPE_ADMIN})
     */
    protected $dateAdded;

    /**
     * @Groups({Scope::SCOPE_PRIVATE, Scope::SCOPE_ADMIN})
     */
    protected $dateVerified;


    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setId(UuidInterface $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): ?EmailAddress
    {
        if (!$this->email instanceof EmailAddress) {
            $this->email = new EmailAddress($this->email);
        }

        return $this->email;
    }

    public function setEmail($email): void
    {
        if (!$email instanceof EmailAddress) {
            $email = new EmailAddress($email);
        }

        $this->email = $email;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getAcceptsCommercialCommunications(): bool
    {
        return $this->acceptsCommercialCommunications;
    }

    public function setAcceptsCommercialCommunications(bool $acceptsCommercialCommunications): void
    {
        $this->acceptsCommercialCommunications = $acceptsCommercialCommunications;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function addRole(string $role): void
    {
       if (!in_array($role, $this->roles)) {
           $this->roles[] = $role;
       }
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function removeRole(string $role): void
    {
        if ($index = array_search($role, $this->roles) !== false) {
            unset($this->roles[$index]);
        }
    }

    /**
     * @return mixed
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    /**
     * @param mixed $subscriptions
     */
    public function setSubscriptions($subscriptions): void
    {
        $this->subscriptions = $subscriptions;
    }

    public function getDateAdded(): \DateTimeImmutable
    {
        return $this->dateAdded;
    }

    public function setDateAdded(\DateTimeImmutable $dateAdded): void
    {
        $this->dateAdded = $dateAdded;
    }

    public function getDateVerified(): ?\DateTimeImmutable
    {
        return $this->dateVerified;
    }

    public function setDateVerified(\DateTimeImmutable $dateVerified): void
    {
        $this->dateVerified = $dateVerified;
    }

    public function getEmailVerificationUrl( string $appUrl, string $secret): string
    {
        $expiry = time()+60*60*24;
        $token = hash_hmac('sha256', json_encode(['expiry' => $expiry, 'id' => (string)$this->id]), $secret);

        return $appUrl . '/verify/' . $token . '/' . $expiry . '/' . (string)$this->id;
    }

    public function getResetPasswordUrl( string $appUrl, string $secret): string
    {
        $expiry = time()+60*60*24;
        $token = hash_hmac('sha256', json_encode(['expiry' => $expiry, 'id' => (string)$this->id]), $secret);

        return $appUrl . '/forgot-password/' . $token . '/' . $expiry . '/' . (string)$this->id;
    }


}
