<?php

declare(strict_types=1);


namespace App\Domain\OAuth2\Repository;

use App\Domain\OAuth2\OAuth2Client;
use App\Domain\User\User;
use Doctrine\Common\Collections\ArrayCollection;

interface OAuth2ClientRepositoryInterface
{
    public function get(int $id): ?OAuth2Client;
    public function getByUser(User $user): ArrayCollection;
}