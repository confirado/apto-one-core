<?php
namespace Apto\Catalog\Application\Core\Query\Shop;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindShops implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $searchString;

    /**
     * FindShops constructor.
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