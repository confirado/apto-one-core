<?php

namespace Apto\Plugins\PricePerUnitElement\Application\Backend\Commands\PricePerUnitItem;

use Apto\Base\Application\Core\CommandInterface;

class RemovePricePerUnitPrice implements CommandInterface
{
    /**
     * @var string
     */
    protected $elementId;

    /**
     * @var string
     */
    private $priceId;

    /**
     * RemovePricePerUnitPrice constructor.
     * @param string $elementId
     * @param string $priceId
     */
    public function __construct(
        string $elementId,
        string $priceId
    ) {
        $this->elementId = $elementId;
        $this->priceId = $priceId;
    }

    /**
     * @return string
     */
    public function getElementId(): string
    {
        return $this->elementId;
    }

    /**
     * @return string
     */
    public function getPriceId(): string
    {
        return $this->priceId;
    }
}