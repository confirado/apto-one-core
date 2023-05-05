<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Pool;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class PoolItemAdded extends AbstractDomainEvent
{
    /**
     * @var AptoUuid
     */
    private $poolItemId;

    /**
     * @var AptoUuid
     */
    private $materialId;

    /**
     * @var AptoUuid
     */
    private $priceGroupId;

    /**
     * PoolItemAdded constructor.
     * @param AptoUuid $id
     * @param AptoUuid $poolItemId
     * @param AptoUuid $materialId
     * @param AptoUuid $priceGroupId
     */
    public function __construct(AptoUuid $id, AptoUuid $poolItemId, AptoUuid $materialId, AptoUuid $priceGroupId)
    {
        parent::__construct($id);
        $this->poolItemId = $poolItemId;
        $this->materialId = $materialId;
        $this->priceGroupId = $priceGroupId;
    }

    /**
     * @return AptoUuid
     */
    public function getPoolItemId(): AptoUuid
    {
        return $this->poolItemId;
    }

    /**
     * @return AptoUuid
     */
    public function getMaterialId(): AptoUuid
    {
        return $this->materialId;
    }

    /**
     * @return AptoUuid
     */
    public function getPriceGroupId(): AptoUuid
    {
        return $this->priceGroupId;
    }
}