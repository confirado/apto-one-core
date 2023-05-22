<?php

namespace Apto\Catalog\Application\Frontend\Query\Configuration;

use Apto\Catalog\Domain\Core\Service\StateValidation\InvalidStateException;

class InvalidConfigurationStateChangeException extends InvalidStateException
{
    /**
     * @param InvalidStateException $e
     * @return self
     */
    public static function fromInvalidConfigurationStateException(InvalidStateException $e): self
    {
        $payload = $e->getPayload();

        return new self(
            $e->getMessage(),
            $payload['product'],
            $payload['section'],
            $payload['element'] ?? null,
            $payload['property'] ?? null,
            $payload['value'] ?? null
        );
    }
}
