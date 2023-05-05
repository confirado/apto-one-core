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
     * @var AptoUuid
     */
    private $productId;

    /**
     * @var State
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
    }

    /**
     * @return string
     */
    public function getProductId(): string
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
     * @return State
     */
    public function getState(): State
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
        if ($parseComputedValues && !$this->productId) {
            throw new NoProductIdGivenException("No ProductId given");
        }
        if ($parseComputedValues && !$this->state) {
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

    /**
     * @param string $formula
     */
    private function parseFormula(string &$formula)
    {
        $pattern = '/\\{.*?\\}/';
        $variables = [];
        preg_match_all($pattern, $formula, $variables);

        foreach ($variables[0] as $variable) {
            $variableName = str_replace('{', '', str_replace('}', '', $variable));

            if (array_key_exists($variableName, $this->computedValues)) {
                $formula = str_replace($variable, $this->computedValues[$variableName], $formula);
            }
        }
        $formula = FunctionParser::parse($formula, $this->computedValues, $this->mediaFileSystem);
    }

}