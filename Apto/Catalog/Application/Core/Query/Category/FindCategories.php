<?php

namespace Apto\Catalog\Application\Core\Query\Category;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindCategories implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $searchString;

    /**
     * FindCategories constructor.
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