<?php
namespace Apto\Catalog\Application\Backend\Commands\Product\Element;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class SetProductElementIsDefault extends ProductChildCommand
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
    private $isDefault;

    /**
     * SetProductElementIsDefault constructor.
     * @param string $productId
     * @param string $sectionId
     * @param string $elementId
     * @param bool $isDefault
     */
    public function __construct(string $productId, string $sectionId, string $elementId, bool $isDefault)
    {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->isDefault = $isDefault;
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
    public function getIsDefault(): bool
    {
        return $this->isDefault;
    }
}