<?php

namespace Apto\Catalog\Application\Core\Query\Product\Element;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindRenderImagesByState implements PublicQueryInterface
{
    /**
     * @var array
     */
    protected $state;

    /**
     * @var array
     */
    protected $perspectives;

    /**
     * @var string
     */
    protected $productId;

    /**
     * FindRenderImageByState constructor.
     * @param array $state
     * @param array $perspectives
     * @param string $productId
     */
    public function __construct(array $state, array $perspectives, string $productId)
    {
        $this->state = $state;
        $this->perspectives = $perspectives;
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
     * @return array
     */
    public function getPerspectives(): array
    {
        return $this->perspectives;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }
}
