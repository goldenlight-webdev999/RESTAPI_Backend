<?php

declare(strict_types=1);

namespace App\Infrastructure\Paypal\Factory;

use App\Infrastructure\Paypal\Entity\Event;
use App\Infrastructure\Paypal\Repository\EventRepository;

final class EventFactory
{
    private $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @param mixed $rawEvent
     * @return Event
     * @throws \Exception
     */
    public function createFromRaw($rawEvent): Event
    {
        /**
         * @var \Paypal\Event $input
         */
        //$input = \Paypal\Event::constructFrom($rawEvent);
        $input = json_decode(json_encode($rawEvent));
        /**
         * @var Event $event
         */
        $event = $this->eventRepository->newInstance();

        /**
         * @var \Paypal\PaypalObject $content
         */
        $content = $input->resource;

        $event->setForeignKey($input->id);
        $event->setType($input->event_type);
        $event->setApiVersion($input->event_version);
        $event->setData((array)$content);
        $event->setDateCreated(new \DateTimeImmutable(date("Y-m-d H:i:s", strtotime($input->create_time))));
        $event->setDateAdded(new \DateTimeImmutable());

        return $event;
    }
}