<?php

declare(strict_types=1);


namespace App\UI\Http\PublicRest\Controllers;

use App\Application\Command\ContactRequest\CreateContactRequest\CreateContactRequestCommand;
use App\Infrastructure\Captcha\CaptchaProvider;
use App\UI\Http\PublicRest\Form\Contact\ContactFormType;
use App\UI\Http\PublicRest\Form\Contact\ContactViewModel;
use DDD\Embeddable\EmailAddress;
use League\Tactician\CommandBus;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class ContactController
{
    /**
     * @Route ("/contact", name="contact", methods={"POST"})
     * @param Request $request
     * @param FormFactoryInterface $formFactory
     * @return JsonResponse
     */
    public function contactForm(Request $request, FormFactoryInterface $formFactory, CommandBus $commandBus, CaptchaProvider $captchaProvider): JsonResponse
    {
        $contactViewModel = new ContactViewModel();
        $form = $formFactory->create(ContactFormType::class, $contactViewModel);

        $content = json_decode($request->getContent(), true);

        unset($content['legal']);
        $form->submit($content);

        $errors = [];

        if ($form->isValid()) {

            if (!$captchaProvider->isCaptchaSolutionValid($contactViewModel->getCaptcha() ?? '')) {
                $errors['captcha'][] = 'Invalid captcha';
            }

            if (!$errors) {
                $command = new CreateContactRequestCommand(
                    $contactViewModel->getName(),
                    new EmailAddress($contactViewModel->getEmail()),
                    $contactViewModel->getPhone(),
                    $contactViewModel->getMessage(),
                    $contactViewModel->isCommercial()
                );

                $commandBus->handle($command);
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
