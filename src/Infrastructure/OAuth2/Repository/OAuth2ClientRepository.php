<?php

declare(strict_types=1);


namespace App\Infrastructure\OAuth2\Repository;


use App\Domain\OAuth2\OAuth2Client;
use App\Domain\OAuth2\Repository\OAuth2ClientRepositoryInterface;
use App\Domain\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method OAuth2Client|null findOneBy(array $criteria, array $orderBy = null)
 */
final class OAuth2ClientRepository extends ServiceEntityRepository implements OAuth2ClientRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, \App\Infrastructure\OAuth2\Entity\OAuth2Client::class);
    }

    public function get(int $id): ?OAuth2Client
    {
        return $this->findOneBy([
            'id' => $id,
        ]);
    }

    public function getByUser(User $user): ArrayCollection
    {
        $result = $this->findBy([
            'user' => $user,
        ]);

        return new ArrayCollection($result);
    }
}