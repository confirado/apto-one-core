<?php

namespace Apto\Plugins\PartsList\Application\Backend\Commands\Part;

use Apto\Base\Application\Core\CommandInterface;

abstract class AddUsage implements CommandInterface
{
    /**
     * @var string
     */
    private $partId;

    /**
     * @var string
     */
    private $usageForUuid;

    /**
     * @var string
     */
    private $quantity;

    /**
     * @var string
     */
    private $productId;

    /**
     * AddUsage constructor.
     * @param string $partId
     * @param string $usageForUuid
     * @param string $quantity
     * @param string $productId
     */
    public function __construct(string $partId, string $usageForUuid, string $quantity, string $productId)
    {
        $this->partId = $partId;
        $this->usageForUuid = $usageForUuid;
        $this->quantity = $quantity;
        $this->productId = $productId;
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
    public function getUsageForUuid(): string
    {
        return $this->usageForUuid;
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
    public function getProductId(): string
    {
        return $this->productId;
    }
}
