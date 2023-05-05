<?php

namespace Apto\Base\Application\Core\Query\MessageBusMessage;

interface MessageBusMessageFinder
{
    /**
     * @return mixed
     */
    public function findMessages();
}