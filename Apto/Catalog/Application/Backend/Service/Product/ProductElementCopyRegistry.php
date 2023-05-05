<?php

namespace Apto\Catalog\Application\Backend\Service\Product;

use PHPUnit\Runner\Exception;

class ProductElementCopyRegistry
{
    /**
     * @var array
     */
    private $productElementCopyProviders;

    public function __construct()
    {
        $this->productElementCopyProviders = [];
    }

    /**
     * @param ProductElementCopyProvider $productElementCopyProvider
     */
    public function addProductElementCopyProvider(ProductElementCopyProvider $productElementCopyProvider)
    {
        $className = get_class($productElementCopyProvider);

        if (array_key_exists($className, $this->productElementCopyProviders)) {
            throw new \InvalidArgumentException(
                'A ProductElementCopyProvider with an id \'' . $className . '\' is already registered.'
            );
        }

        $this->productElementCopyProviders[$className] = $productElementCopyProvider;
    }

    /**
     * @return array
     */
    public function getProductElementCopyProviders(): array
    {
        return $this->productElementCopyProviders;
    }

    public function getProductElementCopyProviderByType(string $type)
    {
        /** @var ProductElementCopyProvider $productElementCopyProvider */
        foreach ($this->productElementCopyProviders as $productElementCopyProvider) {
            if ($productElementCopyProvider->getType() === $type) {
                return $productElementCopyProvider;
            }
        }
        throw new \InvalidArgumentException($type . ' not found');
    }
}
