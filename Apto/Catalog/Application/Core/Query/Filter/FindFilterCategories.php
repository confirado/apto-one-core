<?php

namespace Apto\Catalog\Application\Core\Query\Filter;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindFilterCategories implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $searchString;

    /**
     * FindFilterCategories constructor.
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