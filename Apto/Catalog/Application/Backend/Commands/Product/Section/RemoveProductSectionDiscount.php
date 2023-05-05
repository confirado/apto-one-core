<?php
namespace Apto\Catalog\Application\Backend\Commands\Product\Section;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class RemoveProductSectionDiscount extends ProductChildCommand
{
    /**
     * @var string
     */
    private $sectionId;

    /**
     * @var string
     */
    private $discountId;

    /**
     * RemoveProductSectionDiscount constructor.
     * @param string $productId
     * @param string $sectionId
     * @param string $discountId
     */
    public function __construct(string $productId, string $sectionId, string $discountId)
    {
        parent::__construct($productId);

        $this->sectionId = $sectionId;
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
    public function getDiscountId(): string
    {
        return $this->discountId;
    }
}