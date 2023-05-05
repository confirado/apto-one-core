<?php

namespace Apto\Base\Domain\Core\Model;

abstract class AptoEntity extends AptoEventCapableEntity
{
    /**
     * @return array
     */
    public function getAndClearPublishedEvents(): array
    {
        $events = $this->eventsToPublish;
        $this->eventsToPublish = [];

        return $events;
    }
}