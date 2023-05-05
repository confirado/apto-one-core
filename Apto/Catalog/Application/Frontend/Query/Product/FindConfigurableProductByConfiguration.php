<?php

namespace Apto\Catalog\Application\Frontend\Query\Product;

class FindConfigurableProductByConfiguration extends AbstractFindConfigurableProduct
{
    /**
     * @var string
     */
    private $configurationId;

    /**
     * @var string
     */
    private $configurationType;

    /**
     * FindConfigurableProductByConfiguration constructor.
     * @param string $productId
     * @param string $configurationType
     * @param string $configurationId
     */
    public function __construct(string $productId, string $configurationType, string $configurationId)
    {
        parent::__construct($productId);
        $this->configurationType = $configurationType;
        $this->configurationId = $configurationId;
    }

    /**
     * @return string
     */
    public function getConfigurationId(): string
    {
        return $this->configurationId;
    }

    /**
     * @return string
     */
    public function getConfigurationType(): string
    {
        return $this->configurationType;
    }
}