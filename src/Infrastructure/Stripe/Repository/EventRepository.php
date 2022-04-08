<?php

declare(strict_types=1);


namespace App\Infrastructure\Stripe\Repository;


use App\Infrastructure\Stripe\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class EventRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function get(UuidInterface $uuid): ?Event
    {
        return $this->findOneBy([
            'id' => $uuid->toString(),
        ]);
    }

    public function save(Event $event): void
    {
        $this->_em->persist($event);
        $this->_em->flush($event);
    }

    public function newInstance(): Event
    {
        return new Event();
    }
}