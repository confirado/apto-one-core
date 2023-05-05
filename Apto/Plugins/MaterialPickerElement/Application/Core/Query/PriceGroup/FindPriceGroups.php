<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Query\PriceGroup;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindPriceGroups implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $searchString;

    /**
     * FindPriceGroups constructor.
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