<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Query\Pool;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindPools implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $searchString;

    /**
     * FindPools constructor.
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