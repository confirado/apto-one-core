<?php

namespace Apto\Catalog\Application\Core\Service\Formula;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\ComputedProductValueCalculator;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Service\Formula\FunctionParser;

class FormulaParser
{
    /**
     * @var ComputedProductValueCalculator
     */
    private $computedProductValueCalculator;

    /**
     * @var array
     */
    private $computedValues;

    /**
     * @var MediaFileSystemConnector
     */
    private $mediaFileSystem;

    /**
     * @var AptoUuid|null
     */
    private $productId;

    /**
     * @var State|null
     */
    private $state;

    /**
     * @param ComputedProductValueCalculator $computedProductValueCalculator
     * @param MediaFileSystemConnector $mediaFileSystem
     */
    public function __construct(
        ComputedProductValueCalculator $computedProductValueCalculator,
        MediaFileSystemConnector $mediaFileSystem
    ) {
        $this->computedProductValueCalculator = $computedProductValueCalculator;
        $this->mediaFileSystem = $mediaFileSystem;
        $this->computedValues = [];
        $this->productId = null;
        $this->state = null;
    }

    /**
     * @return string|null
     */
    public function getProductId(): ?string
    {
        return $this->productId;
    }

    /**
     * @param AptoUuid $productId
     * @return FormulaParser
     */
    public function setProductId(AptoUuid $productId): FormulaParser
    {
        $this->productId = $productId;
        return $this;
    }

    /**
     * @return State|null
     */
    public function getState(): ?State
    {
        return $this->state;
    }

    /**
     * @param State $state
     * @return FormulaParser
     */
    public function setState(State $state): FormulaParser
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @param string $formula
     * @param bool $parseComputedValues
     * @param int $precision
     * @return float
     * @throws InvalidUuidException
     * @throws NoProductIdGivenException
     * @throws NoStateGivenException
     */
    public function calculateFormula(string $formula, bool $parseComputedValues = false, int $precision = 0) :float
    {
        if ($parseComputedValues && null !==$this->productId) {
            throw new NoProductIdGivenException("No ProductId given");
        }
        if ($parseComputedValues && null !== $this->state) {
            throw new NoStateGivenException("No State given");
        }
        if ($parseComputedValues) {
            $this->computedValues = $this->computedProductValueCalculator->calculateComputedValues($this->productId->getId(), $this->state);
        }

        $result = \Apto\Catalog\Domain\Core\Service\Formula\FormulaParser::parse(
            $formula,
            $this->computedValues,
            $this->mediaFileSystem
        );

        return round(
            floatval($result),
            $precision
        );
    }
}
