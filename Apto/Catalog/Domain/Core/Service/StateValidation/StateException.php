<?php

namespace Apto\Catalog\Domain\Core\Service\StateValidation;

use InvalidArgumentException;

abstract class StateException extends InvalidArgumentException
{
    /**
     * @var array
     */
    protected $payload = [];

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }
}