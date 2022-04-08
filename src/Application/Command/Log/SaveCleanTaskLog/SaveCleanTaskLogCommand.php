<?php

declare(strict_types=1);


namespace App\Application\Command\Log\SaveCleanTaskLog;


use DDD\Embeddable\IpAddress;
use Ramsey\Uuid\UuidInterface;

final class SaveCleanTaskLogCommand
{
    private $userId;
    private $clientId;
    private $fileName;
    private $finalSize;
    private $originalSize;
    private $clientIp;
    private $userAgent;
    private $executionTime;

    public function __construct(
        ?UuidInterface $userId,
        ?int $clientId,
        string $fileName,
        ?int $finalSize,
        int $originalSize,
        IpAddress $clientIp,
        string $userAgent,
        int $executionTime
    )
    {
        $this->userId = $userId;
        $this->clientId = $clientId;
        $this->fileName = $fileName;
        $this->finalSize = $finalSize;
        $this->originalSize = $originalSize;
        $this->clientIp = $clientIp;
        $this->userAgent = $userAgent;
        $this->executionTime = $executionTime;
    }

    /**
     * @return null|UuidInterface
     */
    public function getUserId(): ?UuidInterface
    {
        return $this->userId;
    }

    /**
     * @return int|null
     */
    public function getClientId(): ?int
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @return int|null
     */
    public function getFinalSize(): ?int
    {
        return $this->finalSize;
    }

    /**
     * @return int
     */
    public function getOriginalSize(): int
    {
        return $this->originalSize;
    }

    /**
     * @return IpAddress
     */
    public function getClientIp(): IpAddress
    {
        return $this->clientIp;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * In milliseconds
     * @return int
     */
    public function getExecutionTime(): int
    {
        return $this->executionTime;
    }
}