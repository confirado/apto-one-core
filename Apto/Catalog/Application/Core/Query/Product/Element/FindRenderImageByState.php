<?php

namespace Apto\Catalog\Application\Core\Query\Product\Element;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindRenderImageByState implements PublicQueryInterface
{
    /**
     * @var array
     */
    protected $state;

    /**
     * @var string
     */
    protected $perspective;

    /**
     * @var string
     */
    protected $productId;

    /**
     * FindRenderImageByState constructor.
     * @param array $state
     * @param string $perspective
     * @param string $productId
     */
    public function __construct(array $state, string $perspective, string $productId)
    {
        $this->state = $state;
        $this->perspective = $perspective;
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
    public function getPerspective(): string
    {
        return $this->perspective;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }
}