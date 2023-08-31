<?php

namespace Apto\Catalog\Application\Core\Service\Formula;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\CircularReferenceException;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\ComputedProductValueCalculator;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Service\Formula\Exception\FormulaParserException;

class FormulaParser
{
    /**
     * @var ComputedProductValueCalculator
     */
    private ComputedProductValueCalculator $computedProductValueCalculator;

    /**
     * @var array
     */
    private array $computedValues;

    /**
     * @var MediaFileSystemConnector
     */
    private MediaFileSystemConnector $mediaFileSystem;

    /**
     * @var AptoUuid|null
     */
    private ?AptoUuid $productId;

    /**
     * @var State|null
     */
    private ?State $state;

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
     * @return AptoUuid|null
     */
    public function getProductId(): ?AptoUuid
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
     * @throws CircularReferenceException
     * @throws FormulaParserException
     */
    public function calculateFormula(string $formula, bool $parseComputedValues = false, int $precision = 0): float
    {
        if ($parseComputedValues && null === $this->productId) {
            throw new NoProductIdGivenException("No ProductId given");
        }
        if ($parseComputedValues && null === $this->state) {
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
