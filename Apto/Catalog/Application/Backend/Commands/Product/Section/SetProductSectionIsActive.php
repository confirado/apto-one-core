<?php
namespace Apto\Catalog\Application\Backend\Commands\Product\Section;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class SetProductSectionIsActive extends ProductChildCommand
{
    /**
     * @var string
     */
    private $sectionId;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * SetProductSectionIsActive constructor.
     * @param string $productId
     * @param string $sectionId
     * @param bool $isActive
     */
    public function __construct(string $productId, string $sectionId, bool $isActive)
    {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->isActive = $isActive;
    }

    /**
     * @return string
     */
    public function getSectionId(): string
    {
        return $this->sectionId;
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }
}