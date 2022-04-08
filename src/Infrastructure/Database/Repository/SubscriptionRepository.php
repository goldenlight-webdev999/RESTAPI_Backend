<?php

declare(strict_types=1);


namespace App\Infrastructure\Database\Repository;


use App\Domain\Subscription\Repository\SubscriptionRepositoryInterface;
use App\Domain\Subscription\Subscription;
use App\Domain\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Subscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscription[]    findAll()
 * @method Subscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class SubscriptionRepository extends ServiceEntityRepository implements SubscriptionRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, \App\Infrastructure\Database\Entity\Subscription::class);
    }

    public function get(UuidInterface $uuid): Subscription
    {
        return $this->findOneBy([
            'id' => $uuid->toString(),
        ]);
    }

    public function save(Subscription $subscription): void
    {
        $this->_em->persist($subscription);
        $this->_em->flush($subscription);
    }

    public function delete(Subscription $subscription): void
    {
        $this->_em->remove($subscription);
        $this->_em->flush($subscription);
    }

    public function newInstance(): Subscription
    {
        return new \App\Infrastructure\Database\Entity\Subscription();
    }

    public function getLiveSubscriptionsByUser(User $user): ArrayCollection
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.user = :userId')
            ->andWhere('p.dateEnd > CURRENT_TIMESTAMP()')
            ->orderBy('p.dateStart', 'ASC')
            ->getQuery();

        $qb->setParameter('userId', $user->getId()->toString());

        return new ArrayCollection($qb->getResult());
    }

    public function getSubscriptionsByUser(User $user): ArrayCollection
    {
        return new ArrayCollection(
            $this->findBy(
                [
                    'user' => $user,
                ],
                [
                    'dateAdded' => 'ASC',
                ]
            )
        );
    }
}