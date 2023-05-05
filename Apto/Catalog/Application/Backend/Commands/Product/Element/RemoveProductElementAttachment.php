<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Element;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class RemoveProductElementAttachment extends ProductChildCommand
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
    private $attachmentId;

    /**
     * RemoveSectionFromProduct constructor.
     * @param string $productId
     * @param string $sectionId
     * @param string $elementId
     * @param string $attachmentId
     */
    public function __construct(string $productId, string $sectionId, string $elementId, string $attachmentId)
    {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->attachmentId = $attachmentId;
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
    public function getAttachmentId(): string
    {
        return $this->attachmentId;
    }
}
