<?php
namespace Apto\Catalog\Application\Backend\Commands\Product\Element;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class AddProductElementDiscount extends ProductChildCommand
{
    /**
     * @var string
     */
    private $sectionId;

    /**
     * @var string
     */
    private $elementId;

    /**
     * @var float
     */
    private $discount;

    /**
     * @var string
     */
    private $customerGroupId;

    /**
     * @var array
     */
    private $name;

    /**
     * AddProductElementDiscount constructor.
     * @param string $productId
     * @param string $sectionId
     * @param string $elementId
     * @param float $discount
     * @param string $customerGroupId
     * @param array $name
     */
    public function __construct(string $productId, string $sectionId, string $elementId, float $discount, string $customerGroupId, array $name)
    {
        parent::__construct($productId);

        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->discount = $discount;
        $this->customerGroupId = $customerGroupId;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSectionId(): string
    {
        return $this->sectionId;
    }

    /**
     * @return string
     */
    public function getElementId(): string
    {
        return $this->elementId;
    }

    /**
     * @return float
     */
    public function getDiscount(): float
    {
        return $this->discount;
    }

    /**
     * @return string
     */
    public function getCustomerGroupId(): string
    {
        return $this->customerGroupId;
    }

    /**
     * @return array
     */
    public function getName(): array
    {
        return $this->name;
    }
}