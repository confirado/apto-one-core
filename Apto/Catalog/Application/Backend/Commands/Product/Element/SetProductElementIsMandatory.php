<?php
namespace Apto\Catalog\Application\Backend\Commands\Product\Element;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class SetProductElementIsMandatory extends ProductChildCommand
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
     * @var bool
     */
    private $isMandatory;

    /**
     * SetProductElementIsMandatory constructor.
     * @param string $productId
     * @param string $sectionId
     * @param string $elementId
     * @param bool $isMandatory
     */
    public function __construct(string $productId, string $sectionId, string $elementId, bool $isMandatory)
    {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
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
     * @return string
     */
    public function getElementId(): string
    {
        return $this->elementId;
    }

    /**
     * @return bool
     */
    public function getIsMandatory(): bool
    {
        return $this->isMandatory;
    }
}