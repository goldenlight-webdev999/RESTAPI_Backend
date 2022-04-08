<?php
declare(strict_types=1);


namespace App\Domain\Log\Repository;


use App\Domain\Log\LogCleanTask;
use App\Domain\OAuth2\OAuth2Client;
use App\Domain\User\User;
use Ramsey\Uuid\UuidInterface;

interface LogCleanTaskRepositoryInterface
{
    public function get(UuidInterface $uuid): ?LogCleanTask;
    public function getByUserId(UuidInterface $userId): array;
    public function save(LogCleanTask $log): void;
    public function newInstance(): LogCleanTask;
    public function getConsumedUploadBandwidthInBytes(User $user, \DateTimeInterface $dateStart, \DateTimeInterface $dateEnd): int;
    public function getConsumedUploadBandwidthByApplicationInBytes(User $user, OAuth2Client $OAuth2Client, \DateTimeInterface $dateStart, \DateTimeInterface $dateEnd): int;
}