<?php

declare(strict_types=1);

namespace App\UI\Http\Rest\Controllers\User;

use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\User;
use App\Infrastructure\Normalization\ObjectNormalizer;
use App\Infrastructure\Paypal\PaypalAdapter;
use League\Tactician\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

final class PaypalController
{
    private $commandBus;
    private $security;
    private $userRepository;
    private $normalizer;
    private $stripeWebhookSigningSecret;
    private $paypalAdapter;

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
        PaypalAdapter $paypalAdapter
    )
    {
        $this->commandBus = $commandBus;
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->normalizer = $normalizer;
        $this->paypalAdapter = $paypalAdapter;
    }

    /**
     * @Route ("/users/{userUuidRaw}/paypal/{priceId}", methods={"GET"})
     * @return JsonResponse
     */
    public function getCheckoutSession(string $priceId, Request $request): JsonResponse
    {

        $subscription = json_decode($this->paypalAdapter->createSubscription(trim($priceId)), true);

        if(isset($subscription['status'])){
            if($subscription['status'] == "APPROVAL_PENDING"){
                foreach ($subscription['links'] as $link){
                    if($link['rel'] == "approve"){

                        return new JsonResponse([
                            'errors' => false,
                            'payload' => [
                                'id' => $subscription['id'],
                                'amount' => 1,
                                'url' => $link['href'],
                            ]
                        ],
                            200
                        );
                    }
                }
            }
        }
        return new JsonResponse([
            'errors' => false,
            'payload' => [
                'id' => "",
                'amount' => "",
                'url' => "",
            ]
        ],
            200
        );
    }


}