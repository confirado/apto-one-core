<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Section;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class RemoveProductSection extends ProductChildCommand
{
    /**
     * @var string
     */
    private $sectionId;

    /**
     * RemoveSectionFromProduct constructor.
     * @param string $productId
     * @param string $sectionId
     */
    public function __construct(string $productId, string $sectionId)
    {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
    }

    /**
     * @return string
     */
    public function getSectionId(): string
    {
        return $this->sectionId;
    }
}