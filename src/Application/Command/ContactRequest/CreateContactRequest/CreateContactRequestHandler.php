<?php

declare(strict_types=1);


namespace App\Application\Command\ContactRequest\CreateContactRequest;


use App\Application\Command\CommandHandlerInterface;
use App\Domain\ContactRequest\Factory\ContactRequestFactory;
use App\Domain\ContactRequest\Repository\ContactRequestRepositoryInterface;
use App\Infrastructure\Mailer\ContactRequestMailer;

final class CreateContactRequestHandler implements CommandHandlerInterface
{
    private $contactRequestFactory;
    private $contactRequestRepository;
    private $contactMailer;

    /**
     * CreateContactRequestHandler constructor.
     * @param ContactRequestRepositoryInterface $contactRequestRepository
     */
    public function __construct(ContactRequestFactory $contactRequestFactory, ContactRequestRepositoryInterface $contactRequestRepository, ContactRequestMailer $contactMailer)
    {
        $this->contactRequestFactory = $contactRequestFactory;
        $this->contactRequestRepository = $contactRequestRepository;
        $this->contactMailer = $contactMailer;
    }

    /**
     * @param CreateContactRequestCommand $command
     * @throws \Exception
     */
    public function handle(CreateContactRequestCommand $command): void
    {
        $contactRequest = $this->contactRequestFactory->createContactRequest(
            $command->getEmail(),
            $command->isAcceptCommercialCommunications(),
            new \DateTimeImmutable()
        );

        $this->contactRequestRepository->save($contactRequest);

        $this->contactMailer->sendNewContactRequest(
            $command->getName(),
            $command->getMessage(),
            $command->getEmail(),
            $command->getPhoneNumber()
        );
    }
}