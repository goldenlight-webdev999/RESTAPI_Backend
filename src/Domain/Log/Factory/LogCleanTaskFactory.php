<?php

declare(strict_types=1);


namespace App\Domain\Log\Factory;


use App\Domain\Log\LogCleanTask;
use App\Domain\Log\Repository\LogCleanTaskRepositoryInterface;
use App\Domain\OAuth2\OAuth2Client;
use App\Domain\User\User;
use DDD\Embeddable\IpAddress;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class LogCleanTaskFactory
{
    private $logTaskRepository;

    /**
     * LogCleanTaskFactory constructor.
     * @param LogCleanTaskRepositoryInterface $logTaskRepository
     */
    public function __construct(LogCleanTaskRepositoryInterface $logTaskRepository)
    {
        $this->logTaskRepository = $logTaskRepository;
    }


    /**
     * @param User|null $user
     * @param OAuth2Client|null $client
     * @param string $fileName
     * @param int|null $finalSize
     * @param int $originalSize
     * @param IpAddress $clientIp
     * @param string $userAgent
     * @param int $excecutionTime
     * @return LogCleanTask
     * @throws \Exception
     */
    public function createLogTask(
        ?User $user,
        ?OAuth2Client $client,
        string $fileName,
        ?int $finalSize,
        int $originalSize,
        IpAddress $clientIp,
        string $userAgent,
        int $excecutionTime
    ): LogCleanTask
    {

        $entity = $this->logTaskRepository->newInstance();
        $entity->setId(Uuid::uuid4());
        $entity->setDateAdded(new \DateTimeImmutable());

        $entity->setUser($user);
        $entity->setOAuthClient($client);
        $entity->setFileName($fileName);
        $entity->setFinalSize($finalSize);
        $entity->setOriginalSize($originalSize);
        $entity->setClientIp($clientIp);
        $entity->setUserAgent($userAgent);
        $entity->setExecutionTime($excecutionTime);

        return $entity;
    }
}