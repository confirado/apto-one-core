<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\ComputedProductValue;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class AddAlias extends ProductChildCommand
{
    /**
     * @var string
     */
    private $computedProductValueId;

    /**
     * @var string
     */
    private $sectionId;

    /**
     * @var string|null
     */
    private $elementId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $property;

    /**
     * @var bool
     */
    private $isCP;

    /**
     * @param string $productId
     * @param string $computedProductValueId
     * @param string $sectionId
     * @param string|null $elementId
     * @param string $name
     * @param string $property
     * @param bool $isCP
     */
    public function __construct(
        string $productId,
        string $computedProductValueId,
        string $sectionId,
        ?string $elementId,
        string $name,
        string $property = '',
        bool $isCP = false
    ) {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->computedProductValueId = $computedProductValueId;
        $this->property = $property;
        $this->name = strtolower($name);
        $this->isCP = $isCP;
    }

    /**
     * @return string
     */
    public function getComputedProductValueId(): string
    {
        return $this->computedProductValueId;
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
    public function getElementId(): ?string
    {
        return $this->elementId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * @return bool
     */
    public function isCP(): bool
    {
        return $this->isCP;
    }
}
