<?php

namespace Apto\Catalog\Application\Backend\Service\Product;

class ProductPluginCopyRegistry
{
    /**
     * @var array
     */
    private array $productPluginCopyProviders;

    public function __construct()
    {
        $this->productPluginCopyProviders = [];
    }

    /**
     * @param ProductPluginCopyProvider $productPluginCopyProvider
     * @return void
     */
    public function addProductPluginCopyProvider(ProductPluginCopyProvider $productPluginCopyProvider)
    {
        $className = get_class($productPluginCopyProvider);
        if (array_key_exists($className, $this->productPluginCopyProviders)) {
            throw new \InvalidArgumentException(
                'A ProductPluginCopyProvider with an id \'' . $className . '\' is already registered.'
            );
        }

        $this->productPluginCopyProviders[$className] = $productPluginCopyProvider;
    }

    /**
     * @return array
     */
    public function getProductPluginCopyProviders(): array
    {
        return $this->productPluginCopyProviders;
    }
}
