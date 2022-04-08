<?php

declare(strict_types=1);


namespace App\Domain\Log;


use App\Domain\OAuth2\OAuth2Client;
use App\Domain\User\User;
use DDD\Embeddable\IpAddress;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Infrastructure\Normalization\ObjectNormalizer as Scope;

class LogCleanTask
{
    /**
     * @var UuidInterface
     * @Groups({Scope::SCOPE_PRIVATE, Scope::SCOPE_ADMIN})
     */
    protected $id;

    /**
     * @var User|null
     * @Groups({Scope::SCOPE_ADMIN})
     */
    protected $user;

    /**
     * @var OAuth2Client|null
     * @Groups({Scope::SCOPE_ADMIN})
     */
    protected $oAuthClient;

    /**
     * @var string
     * @Groups({Scope::SCOPE_PRIVATE, Scope::SCOPE_ADMIN})
     */
    protected $fileName;

    /**
     * @var int
     * @Groups({Scope::SCOPE_PRIVATE, Scope::SCOPE_ADMIN})
     */
    protected $originalSize;

    /**
     * @var int|null
     * @Groups({Scope::SCOPE_ADMIN})
     */
    protected $finalSize;

    /**
     * @var IpAddress
     * @Groups({Scope::SCOPE_PRIVATE, Scope::SCOPE_ADMIN})
     */
    protected $clientIp;

    /**
     * @var string
     * @Groups({Scope::SCOPE_ADMIN})
     */
    protected $userAgent;

    /**
     * In milliseconds
     * @var int
     * @Groups({Scope::SCOPE_ADMIN})
     */
    protected $executionTime;

    /**
     * @var \DateTimeImmutable
     * @Groups({Scope::SCOPE_PRIVATE, Scope::SCOPE_ADMIN})
     */
    protected $dateAdded;

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @param UuidInterface $id
     */
    public function setId(UuidInterface $id): void
    {
        $this->id = $id;
    }

    /**
     * @return null|User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param null|User $user
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return OAuth2Client|null
     */
    public function getOAuthClient(): ?OAuth2Client
    {
        return $this->oAuthClient;
    }

    /**
     * @param OAuth2Client|null $oAuthClient
     */
    public function setOAuthClient($oAuthClient): void
    {
        $this->oAuthClient = $oAuthClient;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    /**
     * @return int
     */
    public function getOriginalSize(): int
    {
        return $this->originalSize;
    }

    /**
     * @param int $originalSize
     */
    public function setOriginalSize(int $originalSize): void
    {
        $this->originalSize = $originalSize;
    }

    /**
     * @return int|null
     */
    public function getFinalSize(): ?int
    {
        return $this->finalSize;
    }

    /**
     * @param int|null $finalSize
     */
    public function setFinalSize(?int $finalSize): void
    {
        $this->finalSize = $finalSize;
    }

    /**
     * @return IpAddress
     */
    public function getClientIp(): IpAddress
    {
        if (!$this->clientIp instanceof IpAddress) {
            $this->clientIp = new IpAddress($this->clientIp);
        }

        return $this->clientIp;
    }

    /**
     * @param IpAddress $clientIp
     */
    public function setClientIp(IpAddress $clientIp): void
    {
        $this->clientIp = $clientIp;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     */
    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }

    /**
     * @return int
     */
    public function getExecutionTime(): int
    {
        return $this->executionTime;
    }

    /**
     * @param int $executionTime
     */
    public function setExecutionTime(int $executionTime): void
    {
        $this->executionTime = $executionTime;
    }


    /**
     * @return \DateTimeImmutable
     */
    public function getDateAdded(): \DateTimeImmutable
    {
        return $this->dateAdded;
    }

    /**
     * @param \DateTimeImmutable $dateAdded
     */
    public function setDateAdded(\DateTimeImmutable $dateAdded): void
    {
        $this->dateAdded = $dateAdded;
    }
}