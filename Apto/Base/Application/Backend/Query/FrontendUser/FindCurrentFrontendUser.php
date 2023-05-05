<?php

namespace Apto\Base\Application\Backend\Query\FrontendUser;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindCurrentFrontendUser implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $username;

    /**
     * FindCurrentUser constructor.
     * @param string $username
     */
    public function __construct(string $username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
}
