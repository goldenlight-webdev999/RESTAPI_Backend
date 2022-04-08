<?php

declare(strict_types=1);


namespace App\Application\Command\Log\SaveCleanTaskLog;


use App\Application\Command\CommandHandlerInterface;
use App\Domain\Log\Factory\LogCleanTaskFactory;
use App\Domain\Log\Repository\LogCleanTaskRepositoryInterface;
use App\Domain\OAuth2\Repository\OAuth2ClientRepositoryInterface;
use App\Domain\User\Repository\UserRepositoryInterface;


final class SaveCleanTaskLogHandler implements CommandHandlerInterface
{
    private $logTaskRepository;
    private $logTaskFactory;
    private $userRepository;
    private $clientRepository;

    /**
     * SaveCleanTaskLogHandler constructor.
     * @param LogCleanTaskRepositoryInterface $logTaskRepository
     * @param LogCleanTaskFactory $logTaskFactory
     * @param UserRepositoryInterface $userRepository
     * @param OAuth2ClientRepositoryInterface $clientRepository
     */
    public function __construct(LogCleanTaskRepositoryInterface $logTaskRepository, LogCleanTaskFactory $logTaskFactory, UserRepositoryInterface $userRepository, OAuth2ClientRepositoryInterface $clientRepository)
    {
        $this->logTaskRepository = $logTaskRepository;
        $this->logTaskFactory = $logTaskFactory;
        $this->userRepository = $userRepository;
        $this->clientRepository = $clientRepository;
    }


    /**
     * @param SaveCleanTaskLogCommand $command
     * @throws \Exception
     */
    public function handle(SaveCleanTaskLogCommand $command): void
    {
        $user = null;
        $client = null;

        try {
            $user = $this->userRepository->get($command->getUserId());
            $client = $this->clientRepository->get($command->getClientId());
        } catch (\Throwable $exception) {

        }

        $entity = $this->logTaskFactory->createLogTask(
            $user,
            $client,
            $this->obfuscateFilename($command->getFileName()),
            $command->getFinalSize(),
            $command->getOriginalSize(),
            $command->getClientIp(),
            $command->getUserAgent(),
            $command->getExecutionTime()
        );

        $this->logTaskRepository->save($entity);
    }

    private function obfuscateFilename(string $originalName): string
    {
        $result = $originalName;
        $inputLength = strlen($originalName);

        if ($inputLength > 7) {
            $inputLength -= 4;
            for ($index = 3; $index < $inputLength; ++$index) {
                $result[$index] = '*';
            }
        }

        return $result;

    }
}