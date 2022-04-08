<?php

declare(strict_types=1);


namespace App\Application\Query\Application\GetUserApplications;


use App\Application\Query\QueryHandlerInterface;
use App\Domain\OAuth2\Repository\OAuth2ClientRepositoryInterface;

final class GetUserApplicationsHandler implements QueryHandlerInterface
{

    private $OAuth2ClientRepository;

    /**
     * GetUserApplicationsHandler constructor.
     */
    public function __construct(OAuth2ClientRepositoryInterface $OAuth2ClientRepository)
    {
        $this->OAuth2ClientRepository = $OAuth2ClientRepository;
    }

    public function handle(GetUserApplicationsQuery $query): array
    {
        $applications = $this->OAuth2ClientRepository->getByUser($query->getUser());

        return $applications->toArray();
    }
}