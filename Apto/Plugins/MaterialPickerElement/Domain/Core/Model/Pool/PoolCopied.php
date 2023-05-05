<?php

namespace Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Pool;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class PoolCopied extends AbstractDomainEvent
{
    /**
     * @var AptoUuid
     */
    private $poolId;

    /**
     * PoolCopied constructor.
     * @param AptoUuid $id
     * @param AptoUuid $poolId
     */
    public function __construct(AptoUuid $id, AptoUuid $poolId)
    {
        parent::__construct($id);
        $this->poolId = $poolId;
    }

    /**
     * @return AptoUuid
     */
    public function getPoolId(): AptoUuid
    {
        return $this->poolId;
    }
}