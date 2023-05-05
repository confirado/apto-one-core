<?php

namespace Apto\Catalog\Application\Frontend\Commands\Configuration;

use Apto\Base\Application\Core\PublicCommandInterface;

abstract class ConfigurationCommand implements PublicCommandInterface
{
    /**
     * @var string
     */
    private $productId;

    /**
     * @var array
     */
    private $state;

    /**
     * ConfigurationCommand constructor.
     * @param string $productId
     * @param array $state
     */
    public function __construct(string $productId, array $state)
    {
        $this->productId = $productId;
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }

    /**
     * @return array
     */
    public function getState(): array
    {
        return $this->state;
    }
}