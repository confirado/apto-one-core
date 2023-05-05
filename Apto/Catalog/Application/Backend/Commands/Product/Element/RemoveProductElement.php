<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Element;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class RemoveProductElement extends ProductChildCommand
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
     * RemoveSectionFromProduct constructor.
     * @param string $productId
     * @param string $sectionId
     * @param string $elementId
     */
    public function __construct(string $productId, string $sectionId, string $elementId)
    {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
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
}