<?php

namespace Apto\Catalog\Application\Core\Query\Product;

use Apto\Base\Application\Core\PublicQueryInterface;

abstract class AbstractFindProductIdByIdentifier implements PublicQueryInterface
{
    /**
     * @var string
     */
    protected $productIdentifier;

    /**
     * FindProductIdByIdentifier constructor.
     * @param string $productIdentifier
     */
    public function __construct(string $productIdentifier)
    {
        $this->productIdentifier = $productIdentifier;
    }

    /**
     * @return string
     */
    public function getProductIdentifier(): string
    {
        return $this->productIdentifier;
    }
}