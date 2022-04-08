<?php

declare(strict_types=1);

namespace App\Infrastructure\Paypal\Listeners;

use App\Application\Command\Subscription\CancelAllSubscriptions\CancelAllSubscriptionsCommand;
use App\Domain\Subscription\Events\UserSubscriptionExpiredEvent;
use App\Domain\Subscription\Events\UserSubscriptionPaidOutEvent;
use App\Infrastructure\EventBus\EventBus;
use App\Infrastructure\Paypal\Entity\Event;
use App\Infrastructure\Paypal\Events\WebhookReceivedEvent;
use App\Infrastructure\Paypal\Factory\EventFactory;
use App\Infrastructure\Paypal\Factory\CustomerSubscriptionFactory;
use App\Infrastructure\Paypal\Repository\CustomerSubscriptionRepository;
use App\Infrastructure\Paypal\Repository\EventRepository;
use App\Infrastructure\Paypal\PaypalSubscriptionTypeAdapter;
use App\Domain\Subscription\ValueObject\SubscriptionTypeEnum;
use App\Domain\Subscription\Subscription;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\User;
use App\Domain\Subscription\Factory\SubscriptionFactory;
use App\Domain\Subscription\Repository\SubscriptionRepositoryInterface;
use App\Infrastructure\Paypal\Repository\CustomerRepository;
use DDD\Embeddable\EmailAddress;
use League\Tactician\CommandBus;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class WebhookListener implements EventSubscriberInterface
{
    private $eventFactory;
    private $commandBus;
    private $eventRepository;
    private $customerSubscriptionRepository;
    private $eventBus;
    private $userRepository;
    private $subscriptionFactory;
    private $customerSubscriptionFactory;
    private $subscriptionRepository;
    private $customerRepository;
    private $paypalSubscriptionTypeAdapter;

    public function __construct(
        EventFactory $eventFactory,
        EventRepository $eventRepository,
        CustomerSubscriptionRepository $customerSubscriptionRepository,
        SubscriptionFactory $subscriptionFactory,
        CustomerSubscriptionFactory $customerSubscriptionFactory,
        SubscriptionRepositoryInterface $subscriptionRepository,
        EventBus $eventBus,
        UserRepositoryInterface $userRepository,
        PaypalSubscriptionTypeAdapter $paypalSubscriptionTypeAdapter,
        CustomerRepository $customerRepository,
        CommandBus $commandBus

    )
    {
        $this->eventFactory = $eventFactory;
        $this->eventRepository = $eventRepository;
        $this->customerSubscriptionRepository = $customerSubscriptionRepository;
        $this->subscriptionFactory = $subscriptionFactory;
        $this->customerSubscriptionFactory = $customerSubscriptionFactory;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->eventBus = $eventBus;
        $this->userRepository = $userRepository;
        $this->paypalSubscriptionTypeAdapter = $paypalSubscriptionTypeAdapter;
        $this->customerRepository = $customerRepository;
        $this->commandBus = $commandBus;
    }


    public static function getSubscribedEvents()
    {
        return [
            WebhookReceivedEvent::getEventName() => [
                ['saveWebhook', 0],
                ['handlePayment', -1],
            ],
        ];
    }

    /**
     * @param WebhookReceivedEvent $event
     * @throws \Exception
     */
    public function saveWebhook(WebhookReceivedEvent $event): void
    {
        $paypalEvent = $this->eventFactory->createFromRaw($event->getWebhookData());
        $this->eventRepository->save($paypalEvent);
    }

    /**
     * @param WebhookReceivedEvent $event
     * @throws \Exception
     */
    public function handlePayment(WebhookReceivedEvent $event): void
    {
        $paypalEvent = $this->eventFactory->createFromRaw($event->getWebhookData());

        $eventData = $event->getWebhookData();


        $subscriptionForeignKey = $eventData['resource']['id'] ?? null;

        $paymentEvent = null;

        if (!is_null($subscriptionForeignKey)) {
            $customerSubscription = $this->customerSubscriptionRepository->getBySubscriptionId($subscriptionForeignKey);

            if ($customerSubscription) {
                // This is a renewal
                $userId = $customerSubscription->getCustomer()->getUser()->getId();

                switch ($paypalEvent->getType()) {
                    case Event::TYPE_INVOICE_PAYMENT_SUCCEEDED:
                        $paymentEvent = new UserSubscriptionPaidOutEvent(
                            $userId,
                            $paypalEvent->getDateCreated()
                        );
                        break;
                    case Event::TYPE_INVOICE_PAYMENT_FAILED:
                        $paymentEvent = new UserSubscriptionExpiredEvent(
                            $userId
                        );
                        break;
                }
            } else {
                // This is new subscription we will cancel all other and set the new one

                $userEmail = new EmailAddress($eventData['resource']['custom_id']);
                $user = $this->userRepository->getByEmail($userEmail);
                if ($user && $paypalEvent->getType() == Event::TYPE_INVOICE_PAYMENT_SUCCEEDED) {
                    // No subscription yet we can create a new one
                    $planId = $eventData['resource']['plan_id'] ?? null;
                    $subscriptionType = SubscriptionTypeEnum::build($this->paypalSubscriptionTypeAdapter->getSubscriptionType($planId));

                    $liveSubscriptions = $this->subscriptionRepository->getLiveSubscriptionsByUser($user);
                    $isUpgrade = false;
                    foreach ($liveSubscriptions as $liveSubscription) {
                        $packages = $liveSubscription->getPackages();
                        $oldSubscriptionKey = array_search($liveSubscription->getType(), $packages);
                        $newSubscriptionKey = array_search($this->paypalSubscriptionTypeAdapter->getSubscriptionType($planId), $packages);
                        $isUpgrade = $newSubscriptionKey > $oldSubscriptionKey;
                    }

                    $this->commandBus->handle(
                        new CancelAllSubscriptionsCommand($user, $isUpgrade, "paypal")
                    );


                    $customer = $this->customerRepository->getByUser($user);
                    if (!$customer) {
                        // Create a customer
                        $customer = $this->customerRepository->newInstance();
                        $customer->setUser($user);
                        $customer->setCustomerKey($eventData['resource']['subscriber']['email_address']);
                        $customer->setDateAdded(new \DateTimeImmutable());
                        $this->customerRepository->save($customer);
                    }

                    $customerSubscription = $this->customerSubscriptionFactory->createSubscription(
                        $customer,
                        $eventData['resource']['id']
                    );

                    try{
                        $this->customerSubscriptionRepository->save($customerSubscription);
                        $subscription = $this->subscriptionFactory->createSubscription(
                            $user,
                            $subscriptionType
                        );
                        $subscription->setDateStart($paypalEvent->getDateCreated());
                        $subscription->setDateEnd($paypalEvent->getDateCreated()->add(new \DateInterval('P1M')));
                        $subscription->setSubscriptionStatus(Subscription::STATUS_ACTIVE);
                        $subscription->setPaymentMethodMetadata(["paypal"]);
                        $this->subscriptionRepository->save($subscription);
                    }catch (Exception $e){

                    }



                }
            }
        }

        if ($paymentEvent) {
            $this->eventBus->dispatchEvent($paymentEvent);
        }
    }
}
