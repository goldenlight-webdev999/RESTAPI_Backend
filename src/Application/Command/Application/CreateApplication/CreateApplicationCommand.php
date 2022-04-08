<?php

declare(strict_types=1);


namespace App\Application\Command\Application\CreateApplication;


use App\Domain\User\User;

final class CreateApplicationCommand
{
    private $user;
    private $name;
    private $logo;
    private $grantTypes;
    private $redirectUris;

    /**
     * CreateApplicationCommand constructor.
     * @param User $user
     * @param string $name
     * @param string|null $logo
     * @param array $grantTypes
     * @param array $redirectUris
     */
    public function __construct(
        User $user,
        string $name,
        ?string $logo,
        array $grantTypes,
        array $redirectUris
    )
    {
        $this->user = $user;
        $this->name = $name;
        $this->logo = $logo;
        $this->grantTypes = $grantTypes;
        $this->redirectUris = $redirectUris;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return null|string
     */
    public function getLogo(): ?string
    {
        return $this->logo;
    }

    /**
     * @return array
     */
    public function getGrantTypes(): array
    {
        return $this->grantTypes;
    }

    /**
     * @return array
     */
    public function getRedirectUris(): array
    {
        return $this->redirectUris;
    }
}