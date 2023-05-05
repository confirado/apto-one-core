<?php
namespace Apto\Catalog\Application\Backend\Commands\Product\Element;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class RemoveProductElementDiscount extends ProductChildCommand
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
     * @var string
     */
    private $discountId;

    /**
     * RemoveProductElementDiscount constructor.
     * @param string $productId
     * @param string $sectionId
     * @param string $elementId
     * @param string $discountId
     */
    public function __construct(string $productId, string $sectionId, string $elementId, string $discountId)
    {
        parent::__construct($productId);

        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->discountId = $discountId;
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
     * @return string
     */
    public function getDiscountId(): string
    {
        return $this->discountId;
    }
}