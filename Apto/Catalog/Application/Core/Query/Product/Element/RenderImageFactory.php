<?php

namespace Apto\Catalog\Application\Core\Query\Product\Element;

use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

interface RenderImageFactory
{
    /**
     * @param State $state
     * @param string|null $productId
     * @return array
     */
    public function getRenderImagesByImageList(State $state, string $productId = null): array;

    /**
     * @param State $state
     * @param string|null $productId
     * @return array
     */
    public function getPerspectivesByState(State $state, string $productId = null): array;
}
