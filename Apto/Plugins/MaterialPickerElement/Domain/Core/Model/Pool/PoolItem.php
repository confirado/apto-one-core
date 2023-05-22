<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Pool;

use Apto\Base\Domain\Core\Model\AptoEntity;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material\Material;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\PriceGroup\PriceGroup;

class PoolItem extends AptoEntity
{
    /**
     * @phpstan-ignore-next-line
     * @var Pool
     */
    private $pool;

    /**
     * @var Material
     */
    private $material;

    /**
     * @var PriceGroup
     */
    private $priceGroup;

    /**
     * PoolItem constructor.
     * @param AptoUuid $id
     * @param Pool $pool
     * @param Material $material
     * @param PriceGroup $priceGroup
     */
    public function __construct(AptoUuid $id, Pool $pool, Material $material, PriceGroup $priceGroup)
    {
        parent::__construct($id);
        $this->pool = $pool;
        $this->material = $material;
        $this->priceGroup = $priceGroup;
    }

    /**
     * @return AptoUuid
     */
    public function getMaterialId(): AptoUuid
    {
        return $this->material->getId();
    }

    /**
     * @return Material
     */
    public function getMaterial(): Material
    {
        return $this->material;
    }

    /**
     * @return PriceGroup
     */
    public function getPriceGroup(): PriceGroup
    {
        return $this->priceGroup;
    }

    /**
     * @param PriceGroup $priceGroup
     */
    public function setPriceGroup(PriceGroup $priceGroup): void
    {
        $this->priceGroup = $priceGroup;
    }
}
