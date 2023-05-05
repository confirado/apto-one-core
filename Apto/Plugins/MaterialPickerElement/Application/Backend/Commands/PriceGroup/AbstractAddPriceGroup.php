<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\PriceGroup;

use Apto\Base\Application\Core\CommandInterface;

abstract class AbstractAddPriceGroup implements CommandInterface
{
    /**
     * @var array
     */
    private $name;

    /**
     * @var array
     */
    private $internalName;

    /**
     * @var float
     */
    private $additionalCharge;

    /**
     * @var array|null
     */
    private $priceMatrix;

    /**
     * AddPriceGroup constructor.
     * @param array $name
     * @param array $internalName
     * @param float $additionalCharge
     * @param array|null $priceMatrix
     */
    public function __construct(array $name, array $internalName, float $additionalCharge, ?array $priceMatrix = null) {
        $this->name = $name;
        $this->internalName = $internalName;
        $this->additionalCharge = $additionalCharge;
        $this->priceMatrix = $priceMatrix;
    }

    /**
     * @return array
     */
    public function getName(): array
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getInternalName(): array
    {
        return $this->internalName;
    }

    /**
     * @return float
     */
    public function getAdditionalCharge(): float
    {
        return $this->additionalCharge;
    }

    /**
     * @return array|null
     */
    public function getPriceMatrix(): ?array
    {
        return $this->priceMatrix;
    }
}