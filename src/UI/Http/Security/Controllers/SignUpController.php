<?php

declare(strict_types=1);


namespace App\UI\Http\Security\Controllers;

use App\Application\Command\User\SignUp\SignUpCommand;
use App\Domain\User\Exceptions\EmailAlreadyExistsException;
use App\Infrastructure\Captcha\CaptchaProvider;
use App\UI\Http\Security\Form\SignUp\SignUpFormType;
use App\UI\Http\Security\Form\SignUp\SignUpViewModel;
use DDD\Embeddable\EmailAddress;
use League\Tactician\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class SignUpController
{
    /**
     * @Route ("/sign-up", name="sign-up", methods={"POST"})
     * @param Request $request
     * @param FormFactoryInterface $formFactory
     * @return JsonResponse
     */
    public function signUp(Request $request, FormFactoryInterface $formFactory, CommandBus $commandBus, CaptchaProvider $captchaProvider): JsonResponse
    {
        $signUpViewModel = new SignUpViewModel();
        $form = $formFactory->create(SignUpFormType::class, $signUpViewModel);

        $content = json_decode($request->getContent(), true);

        $form->submit($content);

        $errors = [];
        $email = null;

        if ($form->isValid()) {

            if (!$captchaProvider->isCaptchaSolutionValid($signUpViewModel->getCaptcha() ?? '')) {
                $errors['captcha'][] = 'Invalid captcha';
            }

            try {
                $email = new EmailAddress($signUpViewModel->getEmail());
            } catch (\InvalidArgumentException $exception) {
                $errors['email'][] = $exception->getMessage();
            }

            if (!$errors) {
                try {
                    $commandBus->handle(new SignUpCommand(
                        $email,
                        $signUpViewModel->getName(),
                        $signUpViewModel->getPassword(),
                        Uuid::uuid4(),
                        $signUpViewModel->getCommercial()
                    ));
                } catch (EmailAlreadyExistsException $exception) {
                    $errors['email'][] = 'Email already in use';
                }
            }
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
        $responseCode = !!$errors ? 400 : 200;

        return new JsonResponse(
            $response,
            $responseCode
        );
    }
}
