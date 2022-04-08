<?php
declare(strict_types=1);


namespace App\Domain\User\Repository;


use App\Domain\User\User;
use DDD\Embeddable\EmailAddress;
use Ramsey\Uuid\UuidInterface;

interface UserRepositoryInterface
{
    public function get(UuidInterface $uuid): ?User;
    public function getByEmail(EmailAddress $emailAddress): ?User;
    public function save(User $user): void;
    public function newInstance(): User;
    public function getClassName();
}