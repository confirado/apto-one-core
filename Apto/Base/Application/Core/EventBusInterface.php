<?php

namespace Apto\Base\Application\Core;

interface EventBusInterface
{
    /**
     * @param EventInterface $message
     * @return void
     */
    public function handle(EventInterface $message);
}