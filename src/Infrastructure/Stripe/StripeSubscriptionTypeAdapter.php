<?php

declare(strict_types=1);


namespace App\Infrastructure\Stripe;


use App\Domain\Subscription\Interfaces\PaymentGatewaySubscriptionTypeAdapterInterface;
use App\Domain\Subscription\Subscription;
use App\Domain\Subscription\ValueObject\SubscriptionTypeEnum;

final class StripeSubscriptionTypeAdapter implements PaymentGatewaySubscriptionTypeAdapterInterface
{
    private const PLAN_BASIC = 'plan_DDneMjlSwJYFNt';
    private const PLAN_ADVANCE = 'plan_DDngdhme80B4Tl';
    private const PLAN_ENTERPRISE = 'plan_DDniYlkC35eG3G';

    public function getPaymentGatewaySubscriptionType(SubscriptionTypeEnum $subscriptionTypeEnum): string
    {
        switch ((string)$subscriptionTypeEnum) {
            case Subscription::TYPE_BASIC:
                return self::PLAN_BASIC;
                break;
            case Subscription::TYPE_ADVANCE:
                return self::PLAN_ADVANCE;
                break;
            case Subscription::TYPE_ENTERPRISE:
                return self::PLAN_ENTERPRISE;
                break;
            default:
                throw new \RuntimeException(sprintf('Subscription type "%s" is not supported', (string)$subscriptionTypeEnum));
        }
    }

    public function getSubscriptionType($paymentGatewaySubscription): SubscriptionTypeEnum
    {
        switch ($paymentGatewaySubscription) {
            case self::PLAN_BASIC:
                return SubscriptionTypeEnum::build(Subscription::TYPE_BASIC);
                break;
            case self::PLAN_ADVANCE:
                return SubscriptionTypeEnum::build(Subscription::TYPE_ADVANCE);
                break;
            case self::PLAN_ENTERPRISE:
                return SubscriptionTypeEnum::build(Subscription::TYPE_ENTERPRISE);
                break;
            default:
                throw new \RuntimeException(sprintf('Plan id "%s" is not supported', $paymentGatewaySubscription));
        }
    }
}
