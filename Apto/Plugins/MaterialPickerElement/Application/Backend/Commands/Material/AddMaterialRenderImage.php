<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Material;

use Apto\Base\Application\Core\CommandInterface;

class AddMaterialRenderImage implements CommandInterface
{
    /**
     * @var string
     */
    private $materialId;

    /**
     * @var string
     */
    private $poolId;

    /**
     * @var int
     */
    private $layer;

    /**
     * @var string
     */
    private $perspective;

    /**
     * @var int
     */
    protected $offsetX;

    /**
     * @var int
     */
    protected $offsetY;

    /**
     * @var string
     */
    protected $file;

    /**
     * AddMaterialRenderImage constructor.
     * @param string $materialId
     * @param string $poolId
     * @param int $layer
     * @param string $perspective
     * @param string $file
     * @param int|null $offsetX
     * @param int|null $offsetY
     */
    public function __construct(string $materialId, string $poolId, int $layer, string $perspective, string $file,  int $offsetX = null, int $offsetY = null)
    {
        $this->materialId = $materialId;
        $this->poolId = $poolId;
        $this->layer = $layer;
        $this->perspective = $perspective;
        $this->file = $file;
        $this->offsetX = $offsetX === null ? 0 : $offsetX;
        $this->offsetY = $offsetY === null ? 0 : $offsetY;
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
    public function getPoolId(): string
    {
        return $this->poolId;
    }

    /**
     * @return int
     */
    public function getLayer(): int
    {
        return $this->layer;
    }

    /**
     * @return string
     */
    public function getPerspective(): string
    {
        return $this->perspective;
    }

    /**
     * @return int
     */
    public function getOffsetX(): int
    {
        return $this->offsetX;
    }

    /**
     * @return int
     */
    public function getOffsetY(): int
    {
        return $this->offsetY;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }
}