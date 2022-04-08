<?php

declare(strict_types=1);


namespace App\UI\Http\Rest\Controllers\User;


use App\Application\Command\Application\CreateApplication\CreateApplicationCommand;
use App\Application\Command\Application\RemoveApplication\RemoveApplicationCommand;
use App\Application\Query\Application\GetUserApplications\GetUserApplicationsQuery;
use App\Application\Query\Application\GetUserApplicationsLifecycleTraffic\GetUserApplicationsLifecycleTrafficQuery;
use App\Domain\OAuth2\OAuth2Client;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\User;
use App\Infrastructure\Normalization\ObjectNormalizer;
use App\UI\Http\Rest\Form\Application\ApplicationFormType;
use App\UI\Http\Rest\Form\Application\ApplicationViewModel;
use League\Tactician\CommandBus;
use OAuth2\OAuth2;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;

final class ApplicationController
{
    private const CURRENT_USER_ALIAS = 'me';

    private $commandBus;
    private $security;
    private $userRepository;
    private $normalizer;

    public function __construct(CommandBus $commandBus, Security $security, UserRepositoryInterface $userRepository, ObjectNormalizer $normalizer)
    {
        $this->commandBus = $commandBus;
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->normalizer = $normalizer;
    }

    private function fixClientInfo(array &$client): void
    {
        $client['id'] = implode('_', [$client['id'], $client['randomId']]);
        unset($client['randomId']);
    }

    /**
     * @Route ("/users/{userUuidRaw}/applications", methods={"GET"})
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

        $applications = $this->commandBus->handle(
            new GetUserApplicationsQuery($user)
        );

        $applicationsTraffic = [];
        foreach ($applications as $application) {
            $index = $application->getId().'_'.$application->getRandomId();
            $applicationsTraffic[$index] = $this->commandBus->handle(new GetUserApplicationsLifecycleTrafficQuery($user, $application));
        }

        $response = $this->normalizer->normalize($applications, ObjectNormalizer::SCOPE_PRIVATE);

        array_walk($response, [$this, 'fixClientInfo']);

        return JsonResponse::create(
            [
                'applications' => $response,
                'traffic' => $applicationsTraffic,
            ]
        );
    }

    /**
     * @Route ("/users/{userUuidRaw}/applications", methods={"POST"})
     * @param string $userUuidRaw
     * @param Request $request
     * @param FormFactoryInterface $formFactory
     * @return JsonResponse
     */
    public function create(string $userUuidRaw, Request $request, FormFactoryInterface $formFactory): JsonResponse
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

        $currentApplications = $this->commandBus->handle(
            new GetUserApplicationsQuery($user)
        );

        if (count($currentApplications) >= $this->getUserLimits($this->security)) {

            $exceededError = [
                'exceeded' => 'You can not setup more integrations.'
            ];

            $response = [
                'errors' => true,
                'payload' => $exceededError,
            ];

            $responseCode = JsonResponse::HTTP_FORBIDDEN;

            return new JsonResponse(
                $response,
                $responseCode
            );
        }

        $applicationViewModel = new ApplicationViewModel();
        $form = $formFactory->create(ApplicationFormType::class, $applicationViewModel);

        $content = json_decode($request->getContent(), true);

        $form->submit($content);

        $errors = [];

        if ($form->isValid()) {
            $command = new CreateApplicationCommand(
                $user,
                $applicationViewModel->getName(),
                null,
                [
                        OAuth2::GRANT_TYPE_AUTH_CODE,
                        OAuth2::GRANT_TYPE_REFRESH_TOKEN,
                        OAuth2::GRANT_TYPE_IMPLICIT,
                        OAuth2::GRANT_TYPE_USER_CREDENTIALS,
                ],
                [
                    $applicationViewModel->getRedirect(),
                ]
            );
            $this->commandBus->handle($command);
        } else {
            foreach ($form as $formField) {
                if (count($formField->getErrors())) {
                    $errors[$formField->getName()] = [];
                    foreach ($formField->getErrors() as $fieldError) {
                        $errors[$formField->getName()][] = $fieldError->getMessage();
                    }
                }
            }
        }

        $response = [
            'errors' => !!$errors,
            'payload' => $errors,
        ];

        $responseCode = !!$errors ? JsonResponse::HTTP_BAD_REQUEST : JsonResponse::HTTP_OK;

        return new JsonResponse(
            $response,
            $responseCode
        );
    }

    /**
     * @Route ("/users/{userUuidRaw}/applications/{applicationId}", methods={"DELETE"})
     * @param string $userUuidRaw
     * @param string $applicationId
     * @return JsonResponse
     */
    public function remove(string $userUuidRaw, string $applicationId): JsonResponse
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

        $userApplications = $this->commandBus->handle(new GetUserApplicationsQuery($user));
        $application = null;

        $applicationIdData = explode('_', $applicationId);

        $realId = (int)reset($applicationIdData);
        $randomId = end($applicationIdData);

        foreach ($userApplications as $userApplication) {
            /**
             * @var OAuth2Client $userApplication
             */
            if ($userApplication->getId() === $realId && $userApplication->getRandomId() === $randomId) {
                $application = $userApplication;
                break;
            }
        }

        if (!$application) {
            return JsonResponse::create('Application not found.', JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->commandBus->handle(
            new RemoveApplicationCommand($application)
        );

        return JsonResponse::create('OK', JsonResponse::HTTP_OK);
    }

    private function getUserLimits(Security $security): float
    {
        $limit = 0;

        switch (true) {
            case $security->isGranted(User::ROLE_ADMIN):
                $limit = INF;
                break;
            case $security->isGranted(User::ROLE_USER_BUSINESS):
                $limit = INF;
                break;
            case $security->isGranted(User::ROLE_USER_PRO):
                $limit = 3;
                break;
        }

        return $limit;
    }
}