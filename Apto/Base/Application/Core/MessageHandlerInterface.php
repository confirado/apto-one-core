<?php

namespace Apto\Base\Application\Core;

interface MessageHandlerInterface
{
    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable;
}