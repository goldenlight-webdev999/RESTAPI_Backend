<?php

declare(strict_types=1);


namespace App\Domain\Subscription\ValueObject;


use App\Domain\Subscription\Subscription;

final class SubscriptionTypeEnum
{
    private $value;

    private function __construct(string $value)
    {
        if (!in_array($value, Subscription::getValidTypes())) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid subscription type value', $value));
        }

        $this->value = $value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public static function build($value): self
    {
        if ($value instanceof SubscriptionTypeEnum) {
            $value = (string)$value;
        }

        return new self($value);
    }

    public function isGreaterThan(self $type): bool
    {
        $thisValueIndex = array_search((string)$this, Subscription::getValidTypes());
        $thatValueIndex = array_search((string)$type, Subscription::getValidTypes());

        return $thisValueIndex > $thatValueIndex;
    }

    public function isLessThan(self $type): bool
    {
        $thisValueIndex = array_search((string)$this, Subscription::getValidTypes());
        $thatValueIndex = array_search((string)$type, Subscription::getValidTypes());

        return $thisValueIndex < $thatValueIndex;
    }

    public function isEquals(self $type): bool
    {
        return (string)$this === (string)$type;
    }
}