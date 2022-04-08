<?php

declare(strict_types=1);


namespace App\Infrastructure\Database\Repository;

use App\Domain\ContactRequest\ContactRequest;
use App\Domain\ContactRequest\Repository\ContactRequestRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;


/**
 * @method ContactRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactRequest[]    findAll()
 * @method ContactRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ContactRequestRepository extends ServiceEntityRepository implements ContactRequestRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, \App\Infrastructure\Database\Entity\ContactRequest::class);
    }

    public function get(UuidInterface $uuid): ?ContactRequest
    {
        return $this->findOneBy([
            'id' => $uuid->toString(),
        ]);
    }

    public function save(ContactRequest $contactRequest): void
    {
        $this->_em->persist($contactRequest);
        $this->_em->flush();
    }

    public function newInstance(): ContactRequest
    {
        return new \App\Infrastructure\Database\Entity\ContactRequest();
    }
}