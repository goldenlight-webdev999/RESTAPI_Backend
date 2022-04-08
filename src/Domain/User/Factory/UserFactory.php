<?php

declare(strict_types=1);


namespace App\Domain\User\Factory;

use App\Domain\User\Exceptions\EmailAlreadyExistsException;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\UserRole;
use App\Infrastructure\Database\Entity\User;
use DDD\Embeddable\EmailAddress;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserFactory
{
    private $userRepository;
    private $passwordEncoder;

    public function __construct(UserRepositoryInterface $userRepository, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param EmailAddress $emailAddress
     * @param string $fullName
     * @param UuidInterface $uuid
     * @param string $password
     * @return \App\Domain\User\User
     * @throws \Exception
     */
    public function createUser(
        EmailAddress $emailAddress,
        string $fullName,
        UuidInterface $uuid,
        string $password,
        bool $acceptsCommercialCommunications
    ): \App\Domain\User\User
    {

        $user = $this->userRepository->newInstance();

        if ($this->userRepository->getByEmail($emailAddress)) {
            throw new EmailAlreadyExistsException();
        }

        /**
         * @var User $user
         */
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $password);

        $user->setId($uuid);
        $user->setUserName($fullName);
        $user->setEmail($emailAddress);
        $user->setPassword($encodedPassword);
        $user->setDateAdded(new \DateTimeImmutable());
        $user->setAcceptsCommercialCommunications($acceptsCommercialCommunications);
        $user->setRoles([\App\Domain\User\User::ROLE_USER]);

        return $user;
    }
}