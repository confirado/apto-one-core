<?php

namespace Apto\Catalog\Application\Core\Query\Configuration;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindCustomerConfigurations implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $searchString;

    /**
     * FindCustomerConfigurations constructor.
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