<?php

declare(strict_types=1);


namespace App\Infrastructure\Stripe\Repository;
use App\Domain\User\User;
use App\Infrastructure\Stripe\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function get(UuidInterface $uuid): ?Customer
    {
        return $this->findOneBy([
            'id' => $uuid->toString(),
        ]);
    }

    public function getByUser(User $user): ?Customer
    {
        return $this->findOneBy([
            'user' => $user,
        ]);
    }

    public function getByCustomerKey(string $customerKey): ?Customer
    {
        return $this->findOneBy([
            'customer_key' => $customerKey,
        ]);
    }

    public function save(Customer $customer): void
    {
        $this->_em->persist($customer);
        $this->_em->flush($customer);
    }

    public function newInstance(): Customer
    {
        return new Customer();
    }
}