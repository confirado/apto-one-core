<?php

namespace Apto\Catalog\Application\Frontend\Query\Configuration;

use Apto\Catalog\Domain\Core\Service\StateValidation\StateException;

class FailedRulesException extends StateException
{
    /**
     * @param string $message
     * @param array $failedRules
     */
    public function __construct(string $message, array $failedRules)
    {
        parent::__construct($message);

        $this->payload = $failedRules;
    }
}