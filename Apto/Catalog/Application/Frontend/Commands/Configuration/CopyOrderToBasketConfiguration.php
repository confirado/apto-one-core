<?php

namespace Apto\Catalog\Application\Frontend\Commands\Configuration;

use Apto\Base\Application\Core\PublicCommandInterface;

class CopyOrderToBasketConfiguration implements PublicCommandInterface
{
    /**
     * @var string
     */
    private $orderConfigurationId;

    /**
     * @var string
     */
    private $basketConfigurationId;

    /**
     * CopyOrderToBasketConfiguration constructor.
     * @param string $orderConfigurationId
     * @param string $basketConfigurationId
     */
    public function __construct(string $orderConfigurationId, string $basketConfigurationId)
    {
        $this->orderConfigurationId = $orderConfigurationId;
        $this->basketConfigurationId = $basketConfigurationId;
    }

    /**
     * @return string
     */
    public function getOrderConfigurationId(): string
    {
        return $this->orderConfigurationId;
    }

    /**
     * @return string
     */
    public function getBasketConfigurationId(): string
    {
        return $this->basketConfigurationId;
    }
}