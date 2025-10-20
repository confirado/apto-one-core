<?php

namespace  Apto\Plugins\PdfGenerator\Application\Core\Subscribers;

use Apto\Catalog\Application\Core\Service\ShopConnector\BasketItem;

class ProductInquiry
{
    /**
     * @var array
     */
    private $formData;

    /**
     * @var array
     */
    private $state;

    /**
     * @var string
     */
    private $productId;

    /**
     * @var integer
     */
    private $quantity;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var array
     */
    private $compressedState;

    /**
     * @var array
     */
    private $shopCurrency;

    /**
     * @var array
     */
    private $displayCurrency;

    /**
     * @var string
     */
    private $customerGroupExternalId;

    /**
     * @var array
     */
    private $renderImages;

    /**
     * @var array
     */
    private $additionalData;

    /**
     * @param BasketItem $basketItem
     * @param int $quantity
     */
    public function __construct(BasketItem $basketItem, int $quantity)
    {
        $this->additionalData = $basketItem->getAdditionalData();
        $this->formData = $this->additionalData['formData'];
        $this->state = $this->additionalData['humanReadableState'];
        $this->productId = $this->additionalData['productId'];
        $this->quantity = $quantity;
        $this->locale = $this->additionalData['locale'];
        $this->compressedState = $this->additionalData['compressedState'];
        $this->shopCurrency = $this->additionalData['shopCurrency'];
        $this->displayCurrency = $this->additionalData['displayCurrency'];
        $this->customerGroupExternalId = $this->additionalData['customerGroupExternalId'];
        $this->renderImages = $basketItem->getImages();
    }

    /**
     * @return array
     */
    public function getFormData(): array
    {
        return $this->formData;
    }

    /**
     * @return array
     */
    public function getState(): array
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @return array
     */
    public function getCompressedState(): array
    {
        return $this->compressedState;
    }

    /**
     * @return array
     */
    public function getShopCurrency(): array
    {
        return $this->shopCurrency;
    }

    /**
     * @return array
     */
    public function getDisplayCurrency(): array
    {
        return $this->displayCurrency;
    }

    /**
     * @return string
     */
    public function getCustomerGroupExternalId(): string
    {
        return $this->customerGroupExternalId;
    }

    /**
     * @return array
     */
    public function getRenderImages(): array
    {
        return $this->renderImages;
    }

    /**
     * @return array
     */
    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }
}
