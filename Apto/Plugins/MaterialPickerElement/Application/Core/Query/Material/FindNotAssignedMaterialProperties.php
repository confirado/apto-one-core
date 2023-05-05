<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Query\Material;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindNotAssignedMaterialProperties implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $materialId;

    /**
     * @var string
     */
    private $searchString;

    /**
     * FindNotAssignedMaterialProperties constructor.
     * @param string $materialId
     * @param string $searchString
     */
    public function __construct(string $materialId, string $searchString = '')
    {
        $this->materialId = $materialId;
        $this->searchString = $searchString;
    }

    /**
     * @return string
     */
    public function getMaterialId(): string
    {
        return $this->materialId;
    }

    /**
     * @return string
     */
    public function getSearchString(): string
    {
        return $this->searchString;
    }
}