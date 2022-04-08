<?php

declare(strict_types=1);


namespace App\UI\Http\PublicRest\Form\Contact;

use Symfony\Component\Validator\Constraints as Assert;

final class ContactViewModel
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(max=255, min=4)
     */
    private $name;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Assert\Length(max=255)
     */
    private $email;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(max=255, min=6)
     */
    private $phone;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(max=10000)
     */
    private $message;
    /**
     * @var bool
     */
    private $commercial;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(max=2048)
     */
    private $captcha;

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
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return bool
     */
    public function isCommercial(): ?bool
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
}