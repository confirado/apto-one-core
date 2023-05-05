<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Element;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class AddProductElementGallery extends ProductChildCommand
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
    private $gallery;

    /**
     * @param string $productId
     * @param string $sectionId
     * @param string $elementId
     * @param array $gallery
     *
     */
    public function __construct(string $productId, string $sectionId, string $elementId, array $gallery)
    {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->gallery = $gallery;
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
    public function getGallery(): array
    {
        return $this->gallery;
    }
}