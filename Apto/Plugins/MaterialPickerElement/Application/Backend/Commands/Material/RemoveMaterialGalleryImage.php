<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Material;

use Apto\Base\Application\Core\CommandInterface;

class RemoveMaterialGalleryImage implements CommandInterface
{
    /**
     * @var string
     */
    private $materialId;

    /**
     * @var string
     */
    private $galleryImageId;

    /**
     * AddMaterial constructor.
     * @param string $materialId
     * @param string $galleryImageId
     */
    public function __construct(string $materialId, string $galleryImageId) {
        $this->materialId = $materialId;
        $this->galleryImageId = $galleryImageId;
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
    public function getGalleryImageId(): string
    {
        return $this->galleryImageId;
    }
}