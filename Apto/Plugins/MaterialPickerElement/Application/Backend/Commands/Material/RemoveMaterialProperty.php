<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Material;

use Apto\Base\Application\Core\CommandInterface;

class RemoveMaterialProperty implements CommandInterface
{
    /**
     * @var string
     */
    private $materialId;

    /**
     * @var null|string
     */
    private $propertyId;

    /**
     * AddMaterialProperty constructor.
     * @param string $materialId
     * @param string $propertyId
     */
    public function __construct(string $materialId, $propertyId) {
        $this->materialId = $materialId;
        $this->propertyId = $propertyId;
    }

    /**
     * @return string
     */
    public function getMaterialId(): string
    {
        return $this->materialId;
    }

    /**
     * @return null|string
     */
    public function getPropertyId()
    {
        return $this->propertyId;
    }
}