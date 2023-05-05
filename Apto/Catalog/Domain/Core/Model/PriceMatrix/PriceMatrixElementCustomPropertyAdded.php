<?php

namespace Apto\Catalog\Domain\Core\Model\PriceMatrix;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\AbstractDomainEvent;

class PriceMatrixElementCustomPropertyAdded extends AbstractDomainEvent
{
    /**
     * @var AptoUuid
     */
    private $priceMatrixElementId;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $value;

    /**
     * @var bool
     */
    private $translatable;

    /**
     * PriceMatrixElementCustomPropertyAdded constructor.
     * @param AptoUuid $id
     * @param AptoUuid $priceMatrixElementId
     * @param string $key
     * @param string $value
     * @param bool $translatable
     */
    public function __construct(AptoUuid $id, AptoUuid $priceMatrixElementId, string $key, string $value, bool $translatable = false)
    {
        parent::__construct($id);
        $this->priceMatrixElementId = $priceMatrixElementId;
        $this->key = $key;
        $this->value = $value;
        $this->translatable = $translatable;
    }

    /**
     * @return AptoUuid
     */
    public function getPriceMatrixElementId(): AptoUuid
    {
        return $this->priceMatrixElementId;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function getTranslatable(): bool
    {
        return $this->translatable;
    }
}