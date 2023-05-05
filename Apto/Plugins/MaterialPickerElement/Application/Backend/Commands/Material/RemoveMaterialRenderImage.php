<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Material;

use Apto\Base\Application\Core\CommandInterface;

class RemoveMaterialRenderImage implements CommandInterface
{
    /**
     * @var string
     */
    private $materialId;

    /**
     * @var string
     */
    private $renderImageId;

    /**
     * RemoveMaterialRenderImage constructor.
     * @param string $materialId
     * @param string $renderImageId
     */
    public function __construct(string $materialId, string $renderImageId)
    {
        $this->materialId = $materialId;
        $this->renderImageId = $renderImageId;
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
    public function getRenderImageId(): string
    {
        return $this->renderImageId;
    }
}