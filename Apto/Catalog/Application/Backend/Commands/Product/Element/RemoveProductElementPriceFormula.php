<?php
namespace Apto\Catalog\Application\Backend\Commands\Product\Element;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class RemoveProductElementPriceFormula extends ProductChildCommand
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
    private $priceFormulaId;

    /**
     * RemoveProductPriceFormula constructor.
     * @param string $productId
     * @param string $sectionId
     * @param string $elementId
     * @param string $priceFormulaId
     */
    public function __construct(string $productId, string $sectionId, string $elementId, string $priceFormulaId)
    {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->priceFormulaId = $priceFormulaId;
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
    public function getPriceFormulaId(): string
    {
        return $this->priceFormulaId;
    }
}