<?php

namespace Apto\Base\Application\Core\Query\Customer;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindCustomers implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $searchString;

    /**
     * FindCustomers constructor.
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