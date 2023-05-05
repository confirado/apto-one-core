<?php

namespace Apto\Base\Application\Core\Query\CustomerGroup;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindCustomerGroups implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $searchString;

    /**
     * FindCustomerGroups constructor.
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