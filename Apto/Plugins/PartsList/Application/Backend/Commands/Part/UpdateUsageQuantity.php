<?php

namespace Apto\Plugins\PartsList\Application\Backend\Commands\Part;

use Apto\Base\Application\Core\CommandInterface;

abstract class UpdateUsageQuantity implements CommandInterface
{
    /**
     * @var string
     */
    private $partId;

    /**
     * @var string
     */
    private $usageId;

    /**
     * @var string
     */
    private $quantity;

    /**
     * @var string
     */
    private $value;

    /**
     * UpdateUsageQuantity constructor.
     * @param string $partId
     * @param string $usageId
     * @param string $quantity
     */
    public function __construct(string $partId, string $usageId, string $quantity, string $value)
    {
        $this->partId = $partId;
        $this->usageId = $usageId;
        $this->quantity = $quantity;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getPartId(): string
    {
        return $this->partId;
    }

    /**
     * @return string
     */
    public function getUsageId(): string
    {
        return $this->usageId;
    }

    /**
     * @return string
     */
    public function getQuantity(): string
    {
        return $this->quantity;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
