<?php

namespace Apto\Catalog\Application\Core\Query\PriceMatrix;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindPriceMatrices implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $searchString;

    /**
     * FindPriceMatrices constructor.
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