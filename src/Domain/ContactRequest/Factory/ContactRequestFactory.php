<?php

declare(strict_types=1);


namespace App\Domain\ContactRequest\Factory;


use App\Domain\ContactRequest\ContactRequest;
use App\Domain\ContactRequest\Repository\ContactRequestRepositoryInterface;
use DDD\Embeddable\EmailAddress;
use Ramsey\Uuid\Uuid;

final class ContactRequestFactory
{
    /**
     * @var ContactRequestRepositoryInterface
     */
    private $contactRequestRepository;

    /**
     * ContactRequestFactory constructor.
     * @param ContactRequestRepositoryInterface $contactRequestRepository
     */
    public function __construct(ContactRequestRepositoryInterface $contactRequestRepository)
    {
        $this->contactRequestRepository = $contactRequestRepository;
    }

    public function createContactRequest(
        EmailAddress $emailAddress,
        bool $acceptCommercialCommunications,
        \DateTimeImmutable $dateAdded
    ): ContactRequest
    {
        $contactRequest = $this->contactRequestRepository->newInstance();

        $contactRequest->setId(Uuid::uuid4());
        $contactRequest->setEmail($emailAddress);
        $contactRequest->setAcceptCommercialCommunications($acceptCommercialCommunications);
        $contactRequest->setDateAdded($dateAdded);

        return $contactRequest;
    }

}