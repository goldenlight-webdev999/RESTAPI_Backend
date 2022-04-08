<?php

declare(strict_types=1);


namespace App\Infrastructure\OAuth2\Provider;


use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\User;
use DDD\Embeddable\EmailAddress;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class UserProvider implements UserProviderInterface
{
    private $userRepository;

    /**
     * UserProvider constructor.
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    public function loadUserByUsername($username)
    {
        try {
            $email = new EmailAddress($username);
        } catch (\InvalidArgumentException $exception) {
            throw new UsernameNotFoundException();
        }

        $user = $this->userRepository->getByEmail($email);

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Expected an instance of %s, but got "%s".', User::class, get_class($user)));
        }
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Expected an instance of %s, but got "%s".', $this->userRepository->getClassName(), get_class($user)));
        }

        if (null === $reloadedUser = $this->userRepository->get($user->getId())) {
            throw new UsernameNotFoundException(sprintf('User with ID "%s" could not be reloaded.', $user->getId()->toString()));
        }

        return $reloadedUser;
    }

    public function supportsClass($class)
    {
        $supportedClass = $this->userRepository->getClassName();

        return $class === $supportedClass || is_subclass_of($class, $supportedClass);
    }
}
