<?php

declare(strict_types=1);

namespace App\UI\Http\Rest\Controllers;

use App\Domain\Log\Repository\LogCleanTaskRepositoryInterface;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\User;
use App\Infrastructure\Normalization\ObjectNormalizer;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


final class HistoryController
{
    private const CURRENT_USER_ALIAS = 'me';

    /**
     * @Route ("/users/{userUuidRaw}/history")
     * @param string $userUuidRaw
     * @return JsonResponse
     */
    public function get(string $userUuidRaw, Security $security, ObjectNormalizer $normalizer, UserRepositoryInterface $userRepository, LogCleanTaskRepositoryInterface $logCleanTaskRepository): JsonResponse
    {
        $scope = ObjectNormalizer::SCOPE_PUBLIC;
        /**
         * @var User $currentUser
         */
        $currentUser = $security->getUser();

        if ($userUuidRaw === self::CURRENT_USER_ALIAS) {
            $userUuidRaw = $currentUser->getId()->toString();
            $scope = ObjectNormalizer::SCOPE_PRIVATE;
        }

        if ($security->isGranted(User::ROLE_ADMIN)) {
            $scope = ObjectNormalizer::SCOPE_ADMIN;
        }

        if (!Uuid::isValid($userUuidRaw)) {
            throw new \InvalidArgumentException();
        }

        $user = $userRepository->get(Uuid::fromString($userUuidRaw));

        $logs = $logCleanTaskRepository->getByUserId($user->getId());

        $logs = array_reverse($logs);

        return new JsonResponse($normalizer->normalize($logs, $scope));

    }
}