<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Element;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class UpdateProductElement extends ProductChildCommand
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
     * @var string|null
     */
    private $elementIdentifier;

    /**
     * @var array
     */
    private $elementName;

    /**
     * @var array
     */
    private $elementDescription;

    /**
     * @var array
     */
    private $elementErrorMessage;

    /**
     * @var array
     */
    private $definition;

    /**
     * @var null|string
     */
    private $previewImage;

    /**
     * @var int
     */
    private $position;

    /**
     * @var float
     */
    private $percentageSurcharge;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * @var bool
     */
    private $isMandatory;

    /**
     * @var bool
     */
    private $isNotAvailable;

    /**
     * @var bool
     */
    private $isZoomable;

    /**
     * @var bool
     */
    private $isDefault;

    /**
     * @var bool
     */
    private $priceMatrixActive;

    /**
     * @var string|null
     */
    private $priceMatrixId;

    /**
     * @var string|null
     */
    private $priceMatrixRow;

    /**
     * @var string|null
     */
    private $priceMatrixColumn;

    /**
     * @var bool
     */
    private $extendedPriceCalculationActive;

    /**
     * @var string
     */
    private $extendedPriceCalculationFormula;

    /**
     * @var string|null
     */
    private $zoomFunction;

    /**
     * @var boolean
     */
    private $openLinksInDialog;

    /**
     * @param string $productId
     * @param string $sectionId
     * @param string $elementId
     * @param string|null $elementIdentifier
     * @param array $elementName
     * @param array $elementDescription
     * @param array $elementErrorMessage
     * @param array $definition
     * @param $previewImage
     * @param int $position
     * @param float $percentageSurcharge
     * @param bool|null $isActive
     * @param bool|null $isMandatory
     * @param bool|null $isZoomable
     * @param bool|null $isDefault
     * @param bool $priceMatrixActive
     * @param string|null $priceMatrixId
     * @param string|null $priceMatrixRow
     * @param string|null $priceMatrixColumn
     * @param bool $extendedPriceCalculationActive
     * @param string $extendedPriceCalculationFormula
     * @param bool|null $isNotAvailable
     * @param string|null $zoomFunction
     * @param bool|null $openLinksInDialog
     */
    public function __construct(
        string $productId,
        string $sectionId,
        string $elementId,
        ?string $elementIdentifier,
        array $elementName,
        array $elementDescription,
        array $elementErrorMessage,
        array $definition = [],
        $previewImage,
        int $position = 0,
        float $percentageSurcharge = 0.0,
        bool $isActive = null,
        bool $isMandatory = null,
        bool $isZoomable = null,
        bool $isDefault = null,
        bool $priceMatrixActive = false,
        string $priceMatrixId = null,
        string $priceMatrixRow = null,
        string $priceMatrixColumn = null,
        bool $extendedPriceCalculationActive = false,
        string $extendedPriceCalculationFormula = '',
        bool $isNotAvailable = null,
        string $zoomFunction = null,
        bool $openLinksInDialog = null
    ) {
        parent::__construct($productId);
        $this->sectionId = $sectionId;
        $this->elementId = $elementId;
        $this->elementIdentifier = $elementIdentifier;
        $this->elementName = $elementName;
        $this->elementDescription = $elementDescription;
        $this->elementErrorMessage = $elementErrorMessage;
        $this->definition = $definition;
        $this->previewImage = $previewImage;
        $this->position = $position;
        $this->percentageSurcharge = $percentageSurcharge;
        $this->isActive = $isActive;
        $this->isMandatory = $isMandatory;
        $this->isZoomable = $isZoomable;
        $this->isDefault = $isDefault;
        $this->priceMatrixActive = $priceMatrixActive;
        $this->priceMatrixId = $priceMatrixId;
        $this->priceMatrixRow = $priceMatrixRow;
        $this->priceMatrixColumn = $priceMatrixColumn;
        $this->extendedPriceCalculationActive = $extendedPriceCalculationActive;
        $this->extendedPriceCalculationFormula = $extendedPriceCalculationFormula;
		$this->isNotAvailable = $isNotAvailable;
		$this->zoomFunction = $zoomFunction;
        $this->openLinksInDialog = $openLinksInDialog;
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
     * @return string|null
     */
    public function getElementIdentifier(): ?string
    {
        return $this->elementIdentifier;
    }

    /**
     * @return array
     */
    public function getElementName(): array
    {
        return $this->elementName;
    }

    /**
     * @return array
     */
    public function getElementDescription(): array
    {
        return $this->elementDescription;
    }

    /**
     * @return array
     */
    public function getElementErrorMessage(): array
    {
        return $this->elementErrorMessage;
    }

    /**
     * @return array
     */
    public function getDefinition(): array
    {
        return $this->definition;
    }

    /**
     * @return null|string
     */
    public function getPreviewImage()
    {
        return $this->previewImage;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return float
     */
    public function getPercentageSurcharge(): float
    {
        return $this->percentageSurcharge;
    }

    /**
     * @return bool|null
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @return bool|null
     */
    public function getIsMandatory()
    {
        return $this->isMandatory;
    }

    /**
     * @return bool|null
     */
    public function getIsNotAvailable()
    {
        return $this->isNotAvailable;
    }

    /**
     * @return bool|null
     */
    public function getIsZoomable()
    {
        return $this->isZoomable;
    }

    /**
     * @return bool|null
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }

    /**
     * @return bool
     */
    public function getPriceMatrixActive(): bool
    {
        return $this->priceMatrixActive;
    }

    /**
     * @return string|null
     */
    public function getPriceMatrixId(): ?string
    {
        return $this->priceMatrixId;
    }

    /**
     * @return string|null
     */
    public function getPriceMatrixRow(): ?string
    {
        return $this->priceMatrixRow;
    }

    /**
     * @return string|null
     */
    public function getPriceMatrixColumn(): ?string
    {
        return $this->priceMatrixColumn;
    }

    /**
     * @return bool
     */
    public function getExtendedPriceCalculationActive()
    {
        return $this->extendedPriceCalculationActive;
    }

    /**
     * @return string
     */
    public function getExtendedPriceCalculationFormula()
    {
        return $this->extendedPriceCalculationFormula;
    }

    /**
     * @return string|null
     */
    public function getZoomFunction(): ?string
    {
        return $this->zoomFunction;
    }

    /**
     * @return bool
     */
    public function getOpenLinksInDialog()
    {
        return $this->openLinksInDialog;
    }
}
