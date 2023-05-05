<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Query\Material;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindMaterials implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $searchString;

    /**
     * FindMaterials constructor.
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