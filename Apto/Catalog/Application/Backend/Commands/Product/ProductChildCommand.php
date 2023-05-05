<?php

namespace Apto\Catalog\Application\Backend\Commands\Product;

use Apto\Base\Application\Core\CommandInterface;

abstract class ProductChildCommand implements CommandInterface
{
    /**
     * @var string
     */
    private $productId;

    /**
     * ProductSectionCommand constructor.
     * @param string $productId
     */
    public function __construct(string $productId)
    {
        $this->productId = $productId;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }
}