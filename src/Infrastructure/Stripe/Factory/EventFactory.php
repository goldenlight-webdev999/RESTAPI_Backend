<?php

declare(strict_types=1);


namespace App\Infrastructure\Stripe\Factory;


use App\Infrastructure\Stripe\Entity\Event;
use App\Infrastructure\Stripe\Repository\EventRepository;

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
         * @var \Stripe\Event $input
         */
        $input = \Stripe\Event::constructFrom($rawEvent);

        /**
         * @var Event $event
         */
        $event = $this->eventRepository->newInstance();

        /**
         * @var \Stripe\StripeObject $content
         */
        $content = $input->data->object;

        $event->setForeignKey($input->id);
        $event->setType($input->type);
        $event->setApiVersion($input->api_version);
        $event->setData((array)$content->__toArray(true));
        $event->setDateCreated(new \DateTimeImmutable(sprintf('@%u', $input->created)));
        $event->setDateAdded(new \DateTimeImmutable());

        return $event;
    }
}