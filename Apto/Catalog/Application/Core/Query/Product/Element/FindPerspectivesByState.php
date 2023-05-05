<?php

namespace Apto\Catalog\Application\Core\Query\Product\Element;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindPerspectivesByState implements PublicQueryInterface
{
    /**
     * @var array
     */
    protected $state;

    /**
     * @var string
     */
    protected $productId;

    /**
     * @param array $state
     * @param string $productId
     */
    public function __construct(array $state, string $productId)
    {
        $this->state = $state;
        $this->productId = $productId;
    }

    /**
     * @return array
     */
    public function getState(): array
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }
}
