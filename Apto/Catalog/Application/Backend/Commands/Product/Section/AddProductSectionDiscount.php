<?php
namespace Apto\Catalog\Application\Backend\Commands\Product\Section;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class AddProductSectionDiscount extends ProductChildCommand
{
    /**
     * @var string
     */
    private $sectionId;

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
     * AddProductSectionDiscount constructor.
     * @param string $productId
     * @param string $sectionId
     * @param float $discount
     * @param string $customerGroupId
     * @param array $name
     */
    public function __construct(string $productId, string $sectionId, float $discount, string $customerGroupId, array $name)
    {
        parent::__construct($productId);

        $this->sectionId = $sectionId;
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