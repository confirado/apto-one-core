<?php

namespace Apto\Plugins\FloatInputElement\Application\Backend\Commands\FloatInputItem;

use Apto\Base\Application\Core\CommandInterface;

class RemoveFloatInputPrice implements CommandInterface
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
     * RemoveFloatInputPrice constructor.
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