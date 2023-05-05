<?php

namespace Apto\Catalog\Domain\Core\Model\Product;

use Apto\Base\Domain\Core\Model\AptoEntity;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFile;
use Apto\Catalog\Domain\Core\Model\Shop\Shop;

class DomainProperties extends AptoEntity
{
    /**
     * @var MediaFile|null
     */
    private ?MediaFile $previewImage;

    /**
     * @var float
     */
    private float $priceModifier;

    /**
     * @var Shop
     */
    private Shop $shop;

    /**
     * @var Product
     */
    private Product $product;


    /**
     * @param Product $product
     * @param Shop $shop
     */
    public function __construct(Product $product, Shop $shop)
    {
        parent::__construct(new AptoUuid());
        $this->product = $product;
        $this->shop = $shop;
        $this->previewImage = null;
        $this->priceModifier = 100;
    }

    /**
     * @param MediaFile|null $previewImage
     * @return $this
     */
    public function setPreviewImage(MediaFile $previewImage = null): DomainProperties
    {
        $this->previewImage = $previewImage;
        return $this;
    }

    /**
     * @return MediaFile|null
     */
    public function getPreviewImage(): ?MediaFile
    {
        return $this->previewImage;
    }

    /**
     * @return DomainProperties
     */
    public function removePreviewImage(): DomainProperties
    {
        $this->previewImage = null;
        return $this;
    }

    /**
     * @return float
     */
    public function getPriceModifier()
    {
        return $this->priceModifier;
    }

    /**
     * @param float $priceModifier
     * @return DomainProperties
     */
    public function setPriceModifier($priceModifier)
    {
        $this->priceModifier = $priceModifier;
        return $this;
    }

    /**
     * @return Shop
     */
    public function getShop(): Shop
    {
        return $this->shop;
    }

    /**
     * @param Shop $shop
     * @return DomainProperties
     */
    public function setShop(Shop $shop): DomainProperties
    {
        $this->shop = $shop;
        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @return DomainProperties
     */
    public function setProduct(Product $product): DomainProperties
    {
        $this->product = $product;
        return $this;
    }

}