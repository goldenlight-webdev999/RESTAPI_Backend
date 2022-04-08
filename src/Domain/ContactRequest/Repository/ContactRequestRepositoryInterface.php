<?php
declare(strict_types=1);


namespace App\Domain\ContactRequest\Repository;


use App\Domain\ContactRequest\ContactRequest;
use Ramsey\Uuid\UuidInterface;

interface ContactRequestRepositoryInterface
{
    public function get(UuidInterface $uuid): ?ContactRequest;
    public function save(ContactRequest $contactRequest): void;
    public function newInstance(): ContactRequest;
}