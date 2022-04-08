<?php

declare(strict_types=1);


namespace App\UI\Http\Rest\Form\Application;


use Symfony\Component\Validator\Constraints as Assert;

final class ApplicationViewModel
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
     * @Assert\Url()
     * @Assert\Length(max=1024, min=4)
     */
    private $redirect;

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRedirect(): ?string
    {
        return $this->redirect;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $redirect
     */
    public function setRedirect(string $redirect): void
    {
        $this->redirect = $redirect;
    }
}