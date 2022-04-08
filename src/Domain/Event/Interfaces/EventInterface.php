<?php

declare(strict_types=1);


namespace App\Domain\Event\Interfaces;


interface EventInterface
{
    public static function getEventName(): string;
}