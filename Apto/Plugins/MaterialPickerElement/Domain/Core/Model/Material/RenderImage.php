<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material;

use Apto\Base\Domain\Core\Model\AptoEntity;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFile;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Pool\Pool;

class RenderImage extends AptoEntity
{
    /**
     * @var int
     */
    protected $layer;

    /**
     * @var string
     */
    protected $perspective;

    /**
     * @var int
     */
    protected $offsetX;

    /**
     * @var int
     */
    protected $offsetY;

    /**
     * @var MediaFile
     */
    protected $mediaFile;

    /**
     * @var Material
     */
    protected $material;

    /**
     * @var Pool
     */
    protected $pool;

    /**
     * RenderImage constructor.
     * @param AptoUuid $id
     * @param int $layer
     * @param string $perspective
     * @param MediaFile $mediaFile
     * @param Material $material
     * @param Pool $pool
     * @param int $offsetX
     * @param int $offsetY
     */
    public function __construct(
        AptoUuid $id,
        int $layer,
        string $perspective,
        MediaFile $mediaFile,
        Material $material,
        Pool $pool,
        int $offsetX,
        int $offsetY
    ) {
        parent::__construct($id);
        $this->layer = $layer;
        $this->perspective = $perspective;
        $this->mediaFile = $mediaFile;
        $this->material = $material;
        $this->pool = $pool;
        $this->offsetX = $offsetX;
        $this->offsetY = $offsetY;
    }

    /**
     * @return int
     */
    public function getLayer(): int
    {
        return $this->layer;
    }

    /**
     * @return int
     */
    public function getOffsetX(): int
    {
        if ($this->offsetX === null) {
            return 0;
        }
        return $this->offsetX;
    }

    /**
     * @return int
     */
    public function getOffsetY(): int
    {
        if ($this->offsetY === null) {
            return 0;
        }
        return $this->offsetY;
    }

    /**
     * @return string
     */
    public function getPerspective(): string
    {
        return $this->perspective;
    }

    /**
     * @return MediaFile
     */
    public function getMediaFile(): MediaFile
    {
        return $this->mediaFile;
    }

    /**
     * @return Material
     */
    public function getMaterial(): Material
    {
        return $this->material;
    }

    /**
     * @return Pool
     */
    public function getPool(): Pool
    {
        return $this->pool;
    }
}