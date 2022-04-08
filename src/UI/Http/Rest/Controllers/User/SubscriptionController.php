<?php

declare(strict_types=1);


namespace App\UI\Http\Rest\Controllers\User;


use App\Application\Command\Subscription\CancelAllSubscriptions\CancelAllSubscriptionsCommand;
use App\Application\Command\Subscription\ChangeSubscriptionPaymentMethod\ChangeSubscriptionPaymentMethodCommand;
use App\Application\Command\Subscription\ChangeSubscriptionPlan\ChangeSubscriptionTypeCommand;
use App\Application\Command\Subscription\CreateSubscription\CreateSubscriptionCommand;
use App\Application\Query\Subscription\GetCurrentLifecycleTraffic\GetCurrentLifecycleTrafficQuery;
use App\Application\Query\Subscription\GetUserLiveSubscriptions\GetUserLiveSubscriptionsQuery;
use App\Domain\Subscription\Subscription;
use App\Domain\Subscription\ValueObject\SubscriptionTypeEnum;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\User;
use App\Infrastructure\Normalization\ObjectNormalizer;
use League\Tactician\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

final class SubscriptionController
{
    private const CURRENT_USER_ALIAS = 'me';

    private const FIELD_TYPE = 'type';
    private const FIELD_TOKEN = 'token';

    private $commandBus;
    private $security;
    private $userRepository;
    private $normalizer;

    /**
     * SubscriptionController constructor.
     * @param CommandBus $commandBus
     * @param Security $security
     * @param UserRepositoryInterface $userRepository
     * @param ObjectNormalizer $normalizer
     */
    public function __construct(
        CommandBus $commandBus,
        Security $security,
        UserRepositoryInterface $userRepository,
        ObjectNormalizer $normalizer
    )
    {
        $this->commandBus = $commandBus;
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->normalizer = $normalizer;
    }


    /**
     * @Route ("/users/{userUuidRaw}/subscriptions", methods={"GET"})
     * @param string $userUuidRaw
     * @return JsonResponse
     */
    public function getList(string $userUuidRaw): JsonResponse
    {
        /**
         * @var User $currentUser
         */
        $currentUser = $this->security->getUser();

        if ($userUuidRaw === self::CURRENT_USER_ALIAS) {
            $userUuidRaw = $currentUser->getId()->toString();
        }

        if (!Uuid::isValid($userUuidRaw)) {
            throw new \InvalidArgumentException();
        }

        $user = $this->userRepository->get(Uuid::fromString($userUuidRaw));

        $subscriptions = $this->commandBus->handle(
            new GetUserLiveSubscriptionsQuery($user)
        );

        $scope = $this->security->isGranted(User::ROLE_ADMIN) ? ObjectNormalizer::SCOPE_ADMIN : ObjectNormalizer::SCOPE_PRIVATE;

        return JsonResponse::create(
            [
                'subscriptions' => $this->normalizer->normalize($subscriptions, $scope),
                'traffic' => $this->commandBus->handle(new GetCurrentLifecycleTrafficQuery($user)),
            ]
        );
    }

    /**
     * @Route ("/users/{userUuidRaw}/subscriptions", methods={"POST"})
     * @param string $userUuidRaw
     * @param Request $request
     * @return JsonResponse
     */
    public function createOrUpdate(string $userUuidRaw, Request $request): JsonResponse
    {
        /**
         * @var User $currentUser
         */
        $currentUser = $this->security->getUser();

        if ($userUuidRaw === self::CURRENT_USER_ALIAS) {
            $userUuidRaw = $currentUser->getId()->toString();
        }

        if (!Uuid::isValid($userUuidRaw)) {
            throw new \InvalidArgumentException();
        }

        $user = $this->userRepository->get(Uuid::fromString($userUuidRaw));

        $rawContent = $request->getContent(false);
        $content = json_decode($rawContent, true);

        $type = SubscriptionTypeEnum::build($content[self::FIELD_TYPE] ?? null);
        $token = $content[self::FIELD_TOKEN] ?? null;

        $currentUserSubscriptions = $this->commandBus->handle(
            new GetUserLiveSubscriptionsQuery($user)
        );

        try {
            if ($type->isEquals(SubscriptionTypeEnum::build(Subscription::TYPE_FREE))) {
                $this->commandBus->handle(
                    new CancelAllSubscriptionsCommand($user, false)
                );
            } else {
                if (!is_null($token)) {
                    $this->commandBus->handle(
                        new CreateSubscriptionCommand(
                            $user,
                            $type,
                            $token
                        )
                    );
                } else {

                    if ($currentUserSubscriptions) {
                        /**
                         * @var Subscription $newerSubscription
                         */
                        $newerSubscription = end($currentUserSubscriptions);
                        if ($newerSubscription->getSubscriptionStatus() === Subscription::STATUS_CANCELLED) {
                            return JsonResponse::create(
                                [
                                    'errors' => true,
                                    'payload' => [
                                        'You must create a new subscription',
                                    ],
                                ],
                                JsonResponse::HTTP_BAD_REQUEST
                            );
                        }
                    }

                    $this->commandBus->handle(
                        new ChangeSubscriptionTypeCommand(
                            $user,
                            $type
                        )
                    );
                }
            }
        } catch (\LogicException $exception) {
            return JsonResponse::create(
                [
                    'errors' => true,
                    'payload' => [
                        $exception->getMessage(),
                    ],
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return JsonResponse::create('OK');
    }

    /**
     * @Route ("/users/{userUuidRaw}/subscriptions", methods={"PATCH"})
     * @param string $userUuidRaw
     * @param Request $request
     * @return JsonResponse
     */
    public function updateToken(string $userUuidRaw, Request $request): JsonResponse
    {
        /**
         * @var User $currentUser
         */
        $currentUser = $this->security->getUser();

        if ($userUuidRaw === self::CURRENT_USER_ALIAS) {
            $userUuidRaw = $currentUser->getId()->toString();
        }

        if (!Uuid::isValid($userUuidRaw)) {
            throw new \InvalidArgumentException();
        }

        $user = $this->userRepository->get(Uuid::fromString($userUuidRaw));

        $rawContent = $request->getContent(false);
        $content = json_decode($rawContent, true);

        $token = $content[self::FIELD_TOKEN] ?? null;

        try {
            $this->commandBus->handle(
                new ChangeSubscriptionPaymentMethodCommand(
                    $user,
                    $token
                )
            );
        } catch (\LogicException $exception) {
            return JsonResponse::create(
                [
                    'errors' => true,
                    'payload' => [
                        $exception->getMessage(),
                    ],
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return JsonResponse::create('OK');
    }
}
