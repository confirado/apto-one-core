<?php

namespace Apto\Plugins\PartsList\Domain\Core\Model\Part;

use Apto\Base\Domain\Core\Model\AptoEntity;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Model\Product\Product;

class PartProductAssociation extends AptoEntity
{
    /**
     * @var int
     */
    private $count;

    /**
     * @var Part
     */
    private $part;

    /**
     * @var Product
     */
    private $product;

    /**
     * PartProductAssociation constructor.
     * @param AptoUuid $id
     * @param Part $part
     * @param Product $product
     */
    public function __construct(AptoUuid $id, Part $part, Product $product)
    {
        parent::__construct($id);
        $this->part = $part;
        $this->product = $product;
        $this->count = 1;
    }

    /**
     * @return Part
     */
    public function getPart(): Part
    {
        return $this->part;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return $this
     */
    public function setCount(int $count): PartProductAssociation
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return $this
     */
    public function addAssoc(): PartProductAssociation
    {
        $this->count++;
        return $this;
    }

    /**
     * @return $this
     */
    public function removeAssoc(): PartProductAssociation
    {
        $this->count--;
        return $this;
    }
}
