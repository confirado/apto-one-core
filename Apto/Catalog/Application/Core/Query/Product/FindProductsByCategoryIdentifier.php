<?php

namespace Apto\Catalog\Application\Core\Query\Product;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindProductsByCategoryIdentifier implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $categoryIdentifier;

    /**
     * FindProductsByCategoryIdentifier constructor.
     * @param string|null $categoryIdentifier
     */
    public function __construct(string $categoryIdentifier = null)
    {
        $this->categoryIdentifier = $categoryIdentifier;
    }
    /**
     * @return string|null
     */
    public function getCategoryIdentifier()
    {
        return $this->categoryIdentifier;
    }
}