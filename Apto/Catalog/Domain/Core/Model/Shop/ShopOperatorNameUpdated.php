<?php

namespace Apto\Catalog\Domain\Core\Model\Shop;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ShopOperatorNameUpdated extends AbstractDomainEvent
{
    /**
     * @var string|null
     */
    private ?string $operatorName;

    /**
     * @param AptoUuid $id
     * @param string|null $operatorName
     */
    public function __construct(AptoUuid $id, ?string $operatorName)
    {
        parent::__construct($id);
        $this->operatorName = $operatorName;
    }

    /**
     * @return string|null
     */
    public function getOperatorName(): ?string
    {
        return $this->operatorName;
    }
}
