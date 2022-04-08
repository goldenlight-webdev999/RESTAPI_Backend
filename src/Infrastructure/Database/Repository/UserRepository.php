<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Repository;

use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\User;
use DDD\Embeddable\EmailAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, \App\Infrastructure\Database\Entity\User::class);
    }

    public function get(UuidInterface $uuid): ?User
    {
        return $this->findOneBy([
            'id' => $uuid->toString(),
        ]);
    }

    public function getByEmail(EmailAddress $emailAddress): ?User
    {
        return $this->findOneBy([
            'email' => $emailAddress,
        ]);
    }

    public function save(User $user): void
    {
        $this->_em->persist($user);
        $this->_em->flush($user);
    }

    public function newInstance(): User
    {
        return new \App\Infrastructure\Database\Entity\User();
    }
}
