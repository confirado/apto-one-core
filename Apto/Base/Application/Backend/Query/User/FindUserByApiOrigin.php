<?php

namespace Apto\Base\Application\Backend\Query\User;

use Apto\Base\Application\Core\QueryInterface;

class FindUserByApiOrigin implements QueryInterface
{
    /**
     * @var string
     */
    private $apiOrigin;

    /**
     * @var bool
     */
    private $secure;

    /**
     * FindUserByApiOrigin constructor.
     * @param string $apiOrigin
     * @param bool $secure
     */
    public function __construct(string $apiOrigin, bool $secure = true)
    {
        $this->apiOrigin = $apiOrigin;
        $this->secure = $secure;
    }

    /**
     * @return string
     */
    public function getApiOrigin(): string
    {
        return $this->apiOrigin;
    }

    /**
     * @return bool
     */
    public function getSecure(): bool
    {
        return $this->secure;
    }
}