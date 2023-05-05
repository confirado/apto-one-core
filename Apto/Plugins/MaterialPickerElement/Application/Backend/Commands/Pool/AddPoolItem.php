<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Pool;

use Apto\Base\Application\Core\CommandInterface;

class AddPoolItem implements CommandInterface
{
    /**
     * @var string
     */
    private $poolId;

    /**
     * @var string
     */
    private $materialId;

    /**
     * @var string
     */
    private $priceGroupId;

    /**
     * RemovePoolItem constructor.
     * @param string $poolId
     * @param string $materialId
     * @param string $priceGroupId
     */
    public function __construct(string $poolId, string $materialId, string $priceGroupId)
    {
        $this->poolId = $poolId;
        $this->materialId = $materialId;
        $this->priceGroupId = $priceGroupId;
    }

    /**
     * @return string
     */
    public function getPoolId(): string
    {
        return $this->poolId;
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
    public function getPriceGroupId(): string
    {
        return $this->priceGroupId;
    }
}