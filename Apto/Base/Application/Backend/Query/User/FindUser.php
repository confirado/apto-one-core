<?php

namespace Apto\Base\Application\Backend\Query\User;

use Apto\Base\Application\Core\QueryInterface;

class FindUser implements QueryInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var bool
     */
    private $secure;

    /**
     * FindUser constructor.
     * @param string $id
     * @param bool $secure
     */
    public function __construct(string $id, bool $secure = true)
    {
        $this->id = $id;
        $this->secure = $secure;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function getSecure(): bool
    {
        return $this->secure;
    }
}