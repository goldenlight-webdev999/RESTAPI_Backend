<?php

declare(strict_types=1);


namespace App\UI\Http\Security\Form\SignUp;
use Symfony\Component\Validator\Constraints as Assert;


final class SignUpViewModel
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(max=255, min=4)
     */
    private $name;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(max=255, min=8)
     */
    private $password;
    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $captcha;

    /**
     * @var bool
     */
    private $commercial;

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getCaptcha(): ?string
    {
        return $this->captcha;
    }

    /**
     * @param string $captcha
     */
    public function setCaptcha(string $captcha): void
    {
        $this->captcha = $captcha;
    }

    /**
     * @return bool
     */
    public function getCommercial(): ?bool
    {
        return $this->commercial;
    }

    /**
     * @param bool $commercial
     */
    public function setCommercial(bool $commercial): void
    {
        $this->commercial = $commercial;
    }
}