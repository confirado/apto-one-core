<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Material;

use Apto\Base\Application\Core\CommandInterface;

class AddMaterialGalleryImage implements CommandInterface
{
    /**
     * @var string
     */
    private $materialId;

    /**
     * @var null|string
     */
    private $galleryImage;

    /**
     * AddMaterial constructor.
     * @param string $materialId
     * @param null|string $galleryImage
     */
    public function __construct(string $materialId, $galleryImage) {
        $this->materialId = $materialId;
        $this->galleryImage = $galleryImage;
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
    public function getGalleryImage()
    {
        return $this->galleryImage;
    }
}