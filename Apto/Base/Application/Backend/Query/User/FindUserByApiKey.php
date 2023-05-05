<?php

namespace Apto\Base\Application\Backend\Query\User;

use Apto\Base\Application\Core\QueryInterface;

class FindUserByApiKey implements QueryInterface
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var bool
     */
    private $secure;

    /**
     * FindUserByApiKey constructor.
     * @param string $apiKey
     * @param bool $secure
     */
    public function __construct(string $apiKey, bool $secure = true)
    {
        $this->apiKey = $apiKey;
        $this->secure = $secure;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @return bool
     */
    public function getSecure(): bool
    {
        return $this->secure;
    }
}