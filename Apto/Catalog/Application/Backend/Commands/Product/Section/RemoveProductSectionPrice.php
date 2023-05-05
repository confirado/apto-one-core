<?php
namespace Apto\Catalog\Application\Backend\Commands\Product\Section;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class RemoveProductSectionPrice extends ProductChildCommand
{
    /**
     * @var string
     */
    private $sectionId;

    /**
     * @var string
     */
    private $priceId;

    /**
     * RemoveProductSectionPrice constructor.
     * @param string $productId
     * @param string $sectionId
     * @param string $priceId
     */
    public function __construct(string $productId, string $sectionId, string $priceId)
    {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->priceId = $priceId;
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
    public function getPriceId(): string
    {
        return $this->priceId;
    }
}