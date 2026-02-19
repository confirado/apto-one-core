<?php

namespace Apto\Plugins\PartsList\Domain\Core\Model\Part\Usage;

use Apto\Base\Domain\Core\Model\AptoEntity;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Plugins\PartsList\Domain\Core\Model\Part\Part;

abstract class Usage extends AptoEntity
{
    /**
     * @var Part
     */
    protected $part;

    /**
     * @var Quantity
     */
    protected $quantity;

    /**
     * @var Value
     */
    protected $value;

    /**
     * @var AptoUuid|null
     */
    protected $productId;

    /**
     * Usage constructor.
     * @param Part $part
     * @param AptoUuid $id
     * @param Quantity $quantity
     * @param Value $value
     * @param AptoUuid|null $productId
     */
    public function __construct(Part $part, AptoUuid $id, Quantity $quantity, Value $value, AptoUuid $productId = null)
    {
        parent::__construct($id);
        $this->part = $part;
        $this->quantity = $quantity;
        $this->value = $value;
        $this->productId = $productId;
    }

    /**
     * @return Quantity
     */
    public function getQuantity(): Quantity
    {
        return $this->quantity;
    }

    /**
     * @param Quantity $quantity
     * @return $this
     */
    public function setQuantity(Quantity $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return Value
     */
    public function getValue(): Value
    {
        return $this->value;
    }

    /**
     * @param Value $value
     * @return $this
     */
    public function setValue(Value $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @param string $productId
     * @return Usage
     */
    public function setProductId(string $productId): Usage
    {
        $this->productId = $productId;
        return $this;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }

    /**
     * @return Part
     */
    public function getPart(): Part
    {
        return $this->part;
    }
}
