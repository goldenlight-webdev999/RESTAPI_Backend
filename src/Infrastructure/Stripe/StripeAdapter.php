<?php

declare(strict_types=1);


namespace App\Infrastructure\Stripe;


use App\Infrastructure\Stripe\Entity\Customer;
use App\Infrastructure\Stripe\Entity\CustomerSubscription;
use App\Infrastructure\Stripe\Repository\CustomerRepository;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\User;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Customer as StripeCustomer;
use Stripe\Error\SignatureVerification as StripeErrorSignatureVerification;
use Stripe\HttpClient\CurlClient;
use Stripe\Subscription as StripeSubscription;
use Stripe\Subscription;
use Stripe\Token as StripeToken;
use Stripe\Token;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

final class StripeAdapter
{
    private const HEADER_WEBHOOK_SIGNATURE = 'stripe-signature';
    private $security;
    private $userRepository;
    private $customerRepository;
    private $appUrl;

    public function __construct(
        string $stripeApiKey,
        string $proxyConnectionUrl,
        string $appUrl,
        Security $security,
        UserRepositoryInterface $userRepository,
        CustomerRepository $customerRepository
    )
    {
        $curl = new CurlClient([
            CURLOPT_PROXY => $proxyConnectionUrl,
        ]);
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->customerRepository = $customerRepository;
        $this->appUrl = $appUrl;

        \Stripe\Stripe::setApiKey($stripeApiKey);
        \Stripe\ApiRequestor::setHttpClient($curl);
    }

    public function isValidWebhookRequest(Request $request, string $endPointSecret): bool
    {
        $signature = $request->headers->get(self::HEADER_WEBHOOK_SIGNATURE);

        try {
            \Stripe\Webhook::constructEvent(
                $request->getContent(),
                $signature,
                $endPointSecret
            );
        } catch (\UnexpectedValueException|StripeErrorSignatureVerification $e) {
            /**
             * Invalid webhook: there is a problem with the content format or the webhook signature
             */
            return false;
        }

        return true;
    }

    public function createCustomer(\App\Domain\User\User $user, array $tokenData): StripeCustomer
    {
        /**
         * @var Token $token
         */
        $token = StripeToken::constructFrom($tokenData);

        /**
         * @var StripeCustomer $customer
         */
        $customer = StripeCustomer::create(
            [
                'description' => sprintf('UserId: %s', $user->getId()->toString()),
                'source' => $token->id,
            ]
        );

        return $customer;
    }

    public function getPaymentMethodDataFromTokenData(array $tokenData): array
    {
        /**
         * @var Token $token
         */
        $token = StripeToken::constructFrom($tokenData);
        /**
         * @var Token $token
         */
        $token = StripeToken::retrieve($token->id);

        return [
            'brand' => $token->card->brand,
            'lastDigits' => $token->card->last4,
            'funding' => $token->card->funding,
        ];
    }

    public function createSubscription(Customer $customer, string $planId): StripeSubscription
    {
        /**
         * @var StripeSubscription $subscription
         */
        $subscription = \Stripe\Subscription::create([
            'customer' => $customer->getCustomerKey(),
            'items' => [
                [
                    'plan' => $planId,
                ],
            ],
        ]);

        return $subscription;
    }

    public function changeSubscriptionPlan(CustomerSubscription $customerSubscription, string $newPlanId): void
    {
        /**
         * @var Subscription $subscription
         */
        $subscription = \Stripe\Subscription::retrieve($customerSubscription->getSubscriptionId());

        \Stripe\Subscription::update(
            $customerSubscription->getSubscriptionId(),
            [
                'cancel_at_period_end' => false,
                'items' => [
                    [
                        'id' => $subscription->items->data[0]->id,
                        'plan' => $newPlanId,
                    ],
                ],
            ]
        );
    }

    /**
     * USE THIS METHOD ONLY WHEN THE USER PERFORM A SUBSCRIPTION UPGRADE
     *
     * This methods emits a invoice for the pending amount for the current subscription, only useful when the
     * subscription changes and there is a pending payment
     *
     * @param CustomerSubscription $customerSubscription
     *
     * @internal Read the documentation before use it! :-@
     */
    public function emitInvoiceForPendingPaymentSubscription(CustomerSubscription $customerSubscription): void
    {
        \Stripe\Invoice::create([
            'customer' => $customerSubscription->getCustomer()->getCustomerKey(),
            'subscription' => $customerSubscription->getSubscriptionId(),
        ]);
    }

    public function cancelSubscription(CustomerSubscription $subscription): void
    {
        /**
         * @var Subscription $stripeSubscription
         */
        \Stripe\Subscription::update($subscription->getSubscriptionId(), [
            'cancel_at_period_end' => true,
        ]);
    }

    public function updateCustomerPaymentMethod(Customer $customer, array $tokenData): void
    {
        /**
         * @var \Stripe\Customer $stripeCustomer
         */
        $stripeCustomer = \Stripe\Customer::retrieve($customer->getCustomerKey());

        /**
         * @var Token $token
         */
        $token = StripeToken::constructFrom($tokenData);

        $stripeCustomer->offsetSet('source', $token->id);
        $stripeCustomer->save();
    }

    public function getCheckoutSession(string $priceId): StripeSession
    {

        $uuid = $this->security->getUser()->getId();

        $session = StripeSession::create([
            'success_url' => $this->appUrl . '/dashboard/subscriptions?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $this->appUrl . '/dashboard/subscription?cancelled=true',
            'payment_method_types' => ['card'],
            'mode' => 'subscription',
            'client_reference_id' => $uuid,
            'allow_promotion_codes' => true,
            'customer_email' => $this->userRepository->get($uuid)->getEmail(),
            'line_items' => [[
                'price' => $priceId,
                'quantity' => 1,
            ]],
        ]);
        return $session;
    }


}
