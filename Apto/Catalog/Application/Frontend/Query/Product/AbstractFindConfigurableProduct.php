<?php

namespace Apto\Catalog\Application\Frontend\Query\Product;

use Apto\Base\Application\Core\PublicQueryInterface;

abstract class AbstractFindConfigurableProduct implements PublicQueryInterface
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