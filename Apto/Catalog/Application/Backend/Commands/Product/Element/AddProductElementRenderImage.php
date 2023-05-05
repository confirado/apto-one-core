<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Element;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class AddProductElementRenderImage extends ProductChildCommand
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
     * @var array
     */
    private $renderImageOptions;

    /**
     * @var array
     */
    private $offsetOptions;

    /**
     * AddProductElementRenderImage constructor.
     * @param string $productId
     * @param string $sectionId
     * @param string $elementId
     * @param array $renderImageOptions
     * @param array $offsetOptions
     */
    public function __construct(string $productId, string $sectionId, string $elementId, array $renderImageOptions, array $offsetOptions)
    {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->renderImageOptions = $renderImageOptions;
        $this->offsetOptions = $offsetOptions;
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
     * @return array
     */
    public function getRenderImageOptions(): array
    {
        return $this->renderImageOptions;
    }

    /**
     * @return array
     */
    public function getOffsetOptions(): array
    {
        return $this->offsetOptions;
    }
}