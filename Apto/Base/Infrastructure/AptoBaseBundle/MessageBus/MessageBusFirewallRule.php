<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus;

interface MessageBusFirewallRule
{
    /**
     * @param $message
     * @return bool
     */
    public function isExecutionAllowed($message): bool;

    /**
     * @param string $messageClass
     * @return bool
     */
    public function isGranted(string $messageClass): bool;
}