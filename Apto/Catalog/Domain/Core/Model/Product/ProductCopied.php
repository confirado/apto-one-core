<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class ProductCopied extends AbstractDomainEvent
{
    /**
     * @var AptoUuid
     */
    private $productId;

    /**
     * @var array
     */
    private $entityMapping;

    /**
     * ProductCopied constructor.
     * @param AptoUuid $id
     * @param AptoUuid $productId
     * @param array $entityMapping
     */
    public function __construct(AptoUuid $id, AptoUuid $productId, array $entityMapping)
    {
        parent::__construct($id);
        $this->productId = $productId;
        $this->entityMapping = $entityMapping;
    }

    /**
     * @return AptoUuid
     */
    public function getProductId(): AptoUuid
    {
        return $this->productId;
    }

    /**
     * @return array
     */
    public function getEntityMapping(): array
    {
        return $this->entityMapping;
    }
}