<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Element;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class AddProductElementAttachment extends ProductChildCommand
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
    private $attachment;

    /**
     * AddProductElementRenderImage constructor.
     * @param string $productId
     * @param string $sectionId
     * @param string $elementId
     * @param array $attachment
     *
     */
    public function __construct(string $productId, string $sectionId, string $elementId, array $attachment)
    {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->attachment = $attachment;
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
    public function getAttachment(): array
    {
        return $this->attachment;
    }
}
