<?php

namespace Apto\Catalog\Application\Frontend\Query\Product;

class FindConfigurableProduct extends AbstractFindConfigurableProduct
{
    /**
     * @var string
     */
    private $productId;

    /**
     * FindConfigurableProduct constructor.
     * @param string|null $productId
     */
    public function __construct(string $productId = null)
    {
        $this->productId = null === $productId ? '' : $productId;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }
}