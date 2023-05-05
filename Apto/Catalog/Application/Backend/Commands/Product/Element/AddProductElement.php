<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Element;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class AddProductElement extends ProductChildCommand
{
    /**
     * @var string
     */
    private $sectionId;

    /**
     * @var string|null
     */
    private $elementIdentifier;

    /**
     * @var array|null
     */
    private $elementName;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * @var bool
     */
    private $isMandatory;

    /**
     * AddProductElement constructor.
     * @param string $productId
     * @param string $sectionId
     * @param string|null $elementIdentifier
     * @param array|null $elementName
     * @param bool $isActive
     * @param bool $isMandatory
     */
    public function __construct(string $productId, string $sectionId, ?string $elementIdentifier, array $elementName = null, bool $isActive = false, bool $isMandatory = false)
    {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->elementIdentifier = $elementIdentifier;
        $this->elementName = $elementName;
        $this->isActive = $isActive;
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
     * @return string|null
     */
    public function getElementIdentifier(): ?string
    {
        return $this->elementIdentifier;
    }

    /**
     * @return array|null
     */
    public function getElementName()
    {
        return $this->elementName;
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @return bool
     */
    public function getIsMandatory(): bool
    {
        return $this->isMandatory;
    }
}