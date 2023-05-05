<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Query\Pool;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindPoolItemsByMaterial implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $materialId;

    /**
     * FindPool constructor.
     * @param string $materialId
     */
    public function __construct(string $materialId)
    {
        $this->materialId = $materialId;
    }

    /**
     * @return string
     */
    public function getMaterialId(): string
    {
        return $this->materialId;
    }
}