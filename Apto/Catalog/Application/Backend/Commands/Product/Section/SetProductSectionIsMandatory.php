<?php
namespace Apto\Catalog\Application\Backend\Commands\Product\Section;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class SetProductSectionIsMandatory extends ProductChildCommand
{
    /**
     * @var string
     */
    private $sectionId;

    /**
     * @var bool
     */
    private $isMandatory;

    /**
     * SetProductSectionIsMandatory constructor.
     * @param string $productId
     * @param string $sectionId
     * @param bool $isMandatory
     */
    public function __construct(string $productId, string $sectionId, bool $isMandatory)
    {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->isMandatory = $isMandatory;
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
    public function getIsMandatory(): bool
    {
        return $this->isMandatory;
    }
}