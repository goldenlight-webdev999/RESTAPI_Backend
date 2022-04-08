<?php

declare(strict_types=1);


namespace App\Infrastructure\Database\Repository;


use App\Domain\Log\LogCleanTask;
use App\Domain\Log\Repository\LogCleanTaskRepositoryInterface;
use App\Domain\OAuth2\OAuth2Client;
use App\Domain\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LogCleanTask|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogCleanTask|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogCleanTask[]    findAll()
 * @method LogCleanTask[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class LogCleanTaskRepository extends ServiceEntityRepository implements LogCleanTaskRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, \App\Infrastructure\Database\Entity\LogCleanTask::class);
    }

    public function get(UuidInterface $uuid): ?LogCleanTask
    {
        return $this->findOneBy([
            'id' => $uuid->toString(),
        ]);
    }

    public function getByUserId(UuidInterface $userId): array
    {
        return $this->findBy(
            [
                'user' => $userId->toString(),
            ],
            [
                'dateAdded' => 'ASC',
            ]
        );
    }

    public function save(LogCleanTask $log): void
    {
        $this->_em->persist($log);
        $this->_em->flush();
    }

    public function newInstance(): LogCleanTask
    {
        return new \App\Infrastructure\Database\Entity\LogCleanTask();
    }

    public function getConsumedUploadBandwidthInBytes(User $user, \DateTimeInterface $dateStart, \DateTimeInterface $dateEnd): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('SUM(p.originalSize) as total')
            ->andWhere('p.user = :userId')
            ->andWhere('p.dateAdded >= :dateStart')
            ->andWhere('p.dateAdded <= :dateEnd')
            ->getQuery();

        $qb->setParameter('userId', $user->getId()->toString());
        $qb->setParameter('dateStart', $dateStart->format('Y-m-d H:i:s'));
        $qb->setParameter('dateEnd', $dateEnd->format('Y-m-d H:i:s'));

        $result = $qb->getSingleScalarResult();

        return (int)$result;
    }

    public function getConsumedUploadBandwidthByApplicationInBytes(User $user, OAuth2Client $OAuth2Client, \DateTimeInterface $dateStart, \DateTimeInterface $dateEnd): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('SUM(p.originalSize) as total')
            ->andWhere('p.user = :userId')
            ->andWhere('p.oAuthClient = :oauthId')
            ->andWhere('p.dateAdded >= :dateStart')
            ->andWhere('p.dateAdded <= :dateEnd')
            ->getQuery();

        $qb->setParameter('userId', $user->getId()->toString());
        $qb->setParameter('oauthId', $OAuth2Client->getid());
        $qb->setParameter('dateStart', $dateStart->format('Y-m-d H:i:s'));
        $qb->setParameter('dateEnd', $dateEnd->format('Y-m-d H:i:s'));

        $result = $qb->getSingleScalarResult();

        return (int)$result;
    }
}