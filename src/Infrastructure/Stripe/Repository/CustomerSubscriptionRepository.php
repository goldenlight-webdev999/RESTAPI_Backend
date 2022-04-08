<?php

declare(strict_types=1);


namespace App\Infrastructure\Stripe\Repository;


use App\Infrastructure\Stripe\Entity\CustomerSubscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CustomerSubscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomerSubscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomerSubscription[]    findAll()
 * @method CustomerSubscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class CustomerSubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CustomerSubscription::class);
    }

    public function get(UuidInterface $uuid): ?CustomerSubscription
    {
        return $this->findOneBy([
            'id' => $uuid->toString(),
        ]);
    }

    public function getBySubscriptionId(string $subscriptionId): ?CustomerSubscription
    {
        return $this->findOneBy([
            'subscriptionId' => $subscriptionId,
        ]);
    }

    public function save(CustomerSubscription $customerSubscription): void
    {
        $this->_em->persist($customerSubscription);
        $this->_em->flush($customerSubscription);
    }

    public function newInstance(): CustomerSubscription
    {
        return new CustomerSubscription();
    }

    public function delete(CustomerSubscription $subscription): void
    {
        $this->_em->remove($subscription);
        $this->_em->flush($subscription);
    }
}
