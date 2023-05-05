<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Element;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class RemoveProductElementGallery extends ProductChildCommand
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
    private $galleryId;

    /**
     * RemoveSectionFromProduct constructor.
     * @param string $productId
     * @param string $sectionId
     * @param string $elementId
     * @param string $galleryId
     */
    public function __construct(string $productId, string $sectionId, string $elementId, string $galleryId)
    {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->galleryId = $galleryId;
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
    public function getGalleryId(): string
    {
        return $this->galleryId;
    }
}