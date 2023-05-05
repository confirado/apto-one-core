<?php

namespace Apto\Base\Application\Backend\Query\UserRole;

use Apto\Base\Application\Core\QueryInterface;

class FindUserRoles implements QueryInterface
{
    /**
     * @var string
     */
    private $searchString;

    /**
     * FindUserRoles constructor.
     * @param string $searchString
     */
    public function __construct(string $searchString = '')
    {
        $this->searchString = $searchString;
    }

    /**
     * @return string
     */
    public function getSearchString(): string
    {
        return $this->searchString;
    }
}