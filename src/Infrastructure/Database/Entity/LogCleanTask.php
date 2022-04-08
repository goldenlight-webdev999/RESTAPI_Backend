<?php

declare(strict_types=1);


namespace App\Infrastructure\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="\App\Infrastructure\Database\Repository\LogCleanTaskRepository")
 */
class LogCleanTask extends \App\Domain\Log\LogCleanTask
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="\App\Infrastructure\OAuth2\Entity\OAuth2Client")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $oAuthClient;

    /**
     * @ORM\Column(type="string")
     */
    protected $fileName;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $finalSize;

    /**
     * @ORM\Column(type="integer")
     */
    protected $originalSize;

    /**
     * @ORM\Embedded(class="DDD\Embeddable\IpAddress")
     * @ORM\Column(unique=false, nullable=false)
     */
    protected $clientIp;

    /**
     * @ORM\Column(type="string")
     */
    protected $userAgent;

    /**
     * @ORM\Column(type="integer")
     */
    protected $executionTime;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    protected $dateAdded;
}