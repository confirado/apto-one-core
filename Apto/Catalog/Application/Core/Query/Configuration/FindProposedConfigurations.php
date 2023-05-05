<?php

namespace Apto\Catalog\Application\Core\Query\Configuration;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindProposedConfigurations implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $productId;

    /**
     * @var string
     */
    private $searchString;

    /**
     * FindProposedConfigurations constructor.
     * @param string $productId
     * @param string $searchString
     */
    public function __construct(string $productId, string $searchString = '')
    {
        $this->productId = $productId;
        $this->searchString = $searchString;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }

    /**
     * @return string
     */
    public function getSearchString(): string
    {
        return $this->searchString;
    }
}