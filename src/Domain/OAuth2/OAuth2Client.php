<?php

declare(strict_types=1);


namespace App\Domain\OAuth2;


use App\Domain\User\User;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Infrastructure\Normalization\ObjectNormalizer as Scope;

class OAuth2Client extends \FOS\OAuthServerBundle\Entity\Client
{
    /**
     * @Groups({Scope::SCOPE_PRIVATE, Scope::SCOPE_ADMIN})
     */
    protected $id;
    /**
     * @Groups({Scope::SCOPE_PRIVATE, Scope::SCOPE_ADMIN})
     */
    protected $name;
    /**
     * @Groups({Scope::SCOPE_PRIVATE, Scope::SCOPE_ADMIN})
     */
    protected $logo;
    protected $user;
    /**
     * @Groups({Scope::SCOPE_PRIVATE, Scope::SCOPE_ADMIN})
     */
    protected $randomId;

    /**
     * @Groups({Scope::SCOPE_PRIVATE, Scope::SCOPE_ADMIN})
     */
    protected $secret;
    /**
     * @Groups({Scope::SCOPE_PRIVATE, Scope::SCOPE_ADMIN})
     */
    protected $redirectUris;
    /**
     * @Groups({Scope::SCOPE_PRIVATE, Scope::SCOPE_ADMIN})
     */
    protected $allowedGrantTypes;

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
     * @return string
     */
    public function getLogo(): ?string
    {
        return $this->logo;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @param mixed $redirectUris
     */
    public function setRedirectUris($redirectUris): void
    {
        $this->redirectUris = $redirectUris;
    }

    /**
     * @param mixed $allowedGrantTypes
     */
    public function setAllowedGrantTypes($allowedGrantTypes): void
    {
        $this->allowedGrantTypes = $allowedGrantTypes;
    }
}