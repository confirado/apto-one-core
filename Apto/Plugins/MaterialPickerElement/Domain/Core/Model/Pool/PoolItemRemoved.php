<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Pool;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class PoolItemRemoved extends AbstractDomainEvent
{
    /**
     * @var AptoUuid
     */
    private $materialId;

    /**
     * PoolItemRemoved constructor.
     * @param AptoUuid $id
     * @param AptoUuid $materialId
     */
    public function __construct(AptoUuid $id, AptoUuid $materialId)
    {
        parent::__construct($id);
        $this->materialId = $materialId;
    }

    /**
     * @return AptoUuid
     */
    public function getMaterialId(): AptoUuid
    {
        return $this->materialId;
    }
}