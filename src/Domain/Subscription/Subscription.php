<?php

declare(strict_types=1);


namespace App\Domain\Subscription;


use App\Domain\User\User;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use App\Infrastructure\Normalization\ObjectNormalizer as Scope;

/**
 * Class Subscription
 * @package App\Domain\Subscription
 */
class Subscription
{
    public const TYPE_FREE = 'free';
    public const TYPE_BASIC = 'basic';
    public const TYPE_ADVANCE = 'advance';
    public const TYPE_ENTERPRISE = 'enterprise';

    public const STATUS_PENDING = 'pending'; // Created but not paid
    public const STATUS_ACTIVE = 'active'; // Paid
    public const STATUS_CANCELLED = 'cancelled'; // The subscription was cancelled
    public const STATUS_EXPIRED = 'expired'; // Pending payment

    /**
     * @var UuidInterface
     * @Groups({Scope::SCOPE_PRIVATE, Scope::SCOPE_ADMIN})
     */
    protected $id;

    /**
     * @var User
     * @Groups({Scope::SCOPE_ADMIN})
     */
    protected $user;

    /**
     * @var array
     * @Groups({Scope::SCOPE_PRIVATE, Scope::SCOPE_ADMIN})
     */
    protected $paymentMethodMetadata;

    /**
     * @var string
     * @Groups({Scope::SCOPE_PRIVATE, Scope::SCOPE_ADMIN})
     */
    protected $subscriptionStatus;


    /**
     * @var string
     * @Groups({Scope::SCOPE_PRIVATE, Scope::SCOPE_ADMIN})
     */
    protected $type;

    /**
     * @var \DateTimeImmutable
     * @Groups({Scope::SCOPE_PRIVATE, Scope::SCOPE_ADMIN})
     */
    protected $dateStart;

    /**
     * @var \DateTimeImmutable
     * @Groups({Scope::SCOPE_PRIVATE, Scope::SCOPE_ADMIN})
     */
    protected $dateEnd;

    /**
     * @var \DateTimeImmutable
     * @Groups({Scope::SCOPE_PRIVATE, Scope::SCOPE_ADMIN})
     */
    protected $dateAdded;

    /**
     * @var \DateTimeImmutable
     * @Groups({Scope::SCOPE_ADMIN})
     */
    protected $dateUpdated;

    /**
     * !!!
     * PLEASE, preserve the right order
     * Currently it is ordered from the most cheaper to most more expensive
     * !!!
     * @return array
     */
    public static function getValidTypes(): array
    {
        return [
            self::TYPE_FREE,
            self::TYPE_BASIC,
            self::TYPE_ADVANCE,
            self::TYPE_ENTERPRISE,
        ];
    }

    public static function getValidStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_ACTIVE,
            self::STATUS_CANCELLED,
            self::STATUS_EXPIRED,
        ];
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @param UuidInterface $id
     */
    public function setId(UuidInterface $id): void
    {
        $this->id = $id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        if (!in_array($type, self::getValidTypes())) {
            throw new \InvalidArgumentException(sprintf('%s is not a valid type', $type));
        }

        $this->type = $type;
    }

    /**
     * @return array
     */
    public function getPaymentMethodMetadata(): ?array
    {
        return $this->paymentMethodMetadata;
    }

    /**
     * @param array $paymentMethodMetadata
     */
    public function setPaymentMethodMetadata(array $paymentMethodMetadata): void
    {
        $this->paymentMethodMetadata = $paymentMethodMetadata;
    }

    /**
     * @return string
     */
    public function getSubscriptionStatus(): string
    {
        return $this->subscriptionStatus;
    }

    /**
     * @param mixed $subscriptionStatus
     */
    public function setSubscriptionStatus($subscriptionStatus): void
    {
        if (!in_array($subscriptionStatus, self::getValidStatuses())) {
            throw new \InvalidArgumentException(sprintf('%s is not a valid status', $subscriptionStatus));
        }

        $this->subscriptionStatus = $subscriptionStatus;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDateStart(): \DateTimeImmutable
    {
        return $this->dateStart;
    }

    /**
     * @param \DateTimeImmutable $dateStart
     */
    public function setDateStart(\DateTimeImmutable $dateStart): void
    {
        $this->dateStart = $dateStart;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDateEnd(): \DateTimeImmutable
    {
        return $this->dateEnd;
    }

    /**
     * @param \DateTimeImmutable $dateEnd
     */
    public function setDateEnd(\DateTimeImmutable $dateEnd): void
    {
        $this->dateEnd = $dateEnd;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDateAdded(): \DateTimeImmutable
    {
        return $this->dateAdded;
    }

    /**
     * @param \DateTimeImmutable $dateAdded
     */
    public function setDateAdded(\DateTimeImmutable $dateAdded): void
    {
        $this->dateAdded = $dateAdded;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDateUpdated(): \DateTimeImmutable
    {
        return $this->dateUpdated;
    }

    /**
     * @param \DateTimeImmutable $dateUpdated
     */
    public function setDateUpdated(\DateTimeImmutable $dateUpdated): void
    {
        $this->dateUpdated = $dateUpdated;
    }

    /**
     * Returns list of packages in order of value
     * used for checking if plan upgrade
     */
    public function getPackages(): array
    {
        return [
            Subscription::TYPE_FREE,
            Subscription::TYPE_BASIC,
            Subscription::TYPE_ADVANCE,
            Subscription::TYPE_ENTERPRISE,
        ];
    }

}
