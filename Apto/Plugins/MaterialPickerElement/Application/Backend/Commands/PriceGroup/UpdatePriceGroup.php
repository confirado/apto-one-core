<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\PriceGroup;

class UpdatePriceGroup extends AbstractAddPriceGroup
{
    /**
     * @var string
     */
    private $id;

    /**
     * UpdatePriceGroup constructor.
     * @param string $id
     * @param array $name
     * @param array $internalName
     * @param float $additionalCharge
     * @param array|null $priceMatrix
     */
    public function __construct(string $id, array $name, array $internalName, float $additionalCharge, ?array $priceMatrix = null)
    {
        parent::__construct($name, $internalName, $additionalCharge, $priceMatrix);
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}