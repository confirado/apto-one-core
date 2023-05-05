<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Element;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class RemoveProductElementRenderImage extends ProductChildCommand
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
    private $renderImageId;

    /**
     * RemoveSectionFromProduct constructor.
     * @param string $productId
     * @param string $sectionId
     * @param string $elementId
     * @param string $renderImageId
     */
    public function __construct(string $productId, string $sectionId, string $elementId, string $renderImageId)
    {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->renderImageId = $renderImageId;
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
    public function getRenderImageId(): string
    {
        return $this->renderImageId;
    }
}