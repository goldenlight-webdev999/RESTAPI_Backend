<?php

declare(strict_types=1);


namespace App\UI\Http\Rest\Controllers\User;


use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\User;
use App\Infrastructure\Normalization\ObjectNormalizer;
use App\Infrastructure\Stripe\StripeAdapter;
use League\Tactician\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

final class StripeController
{
    private const CURRENT_USER_ALIAS = 'me';

    private const FIELD_TYPE = 'type';
    private const FIELD_TOKEN = 'token';

    private $commandBus;
    private $security;
    private $userRepository;
    private $normalizer;
    private $stripeWebhookSigningSecret;
    private $stripeAdapter;

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
        ObjectNormalizer $normalizer,
        string $stripeWebhookSigningSecret,
        StripeAdapter $stripeAdapter
    )
    {
        $this->commandBus = $commandBus;
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->normalizer = $normalizer;
        $this->stripeWebhookSigningSecret = $stripeWebhookSigningSecret;
        $this->stripeAdapter = $stripeAdapter;
    }

    /**
     * @Route ("/users/{userUuidRaw}/checkout/{priceId}", methods={"GET"})
     * @return JsonResponse
     */
    public function getCheckoutSession(string $priceId, Request $request): JsonResponse
    {

        $errors = [];

        try {
            $checkoutSession = $this->stripeAdapter->getCheckoutSession($priceId, $this->stripeWebhookSigningSecret);
        } catch (\Throwable $exception) {
            return new JsonResponse([
                'errors' => true,
                //'payload' => ['Payment session could not be established'],
                'payload' => [$exception->getMessage()],
            ], JsonResponse::HTTP_BAD_REQUEST);
        }


        return new JsonResponse([
                'errors' => false,
                'payload' => [
                    'id' => $checkoutSession->id,
                    'amount' => $checkoutSession->amount_total,
                    'url' => $checkoutSession->url,
                ]
            ],
            200
        );
    }

}

