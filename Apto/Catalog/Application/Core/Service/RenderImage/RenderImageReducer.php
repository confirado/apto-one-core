<?php

namespace Apto\Catalog\Application\Core\Service\RenderImage;

use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

interface RenderImageReducer
{
    /**
     * @param string $perspective
     * @param State $state
     * @param array $imageList
     * @param string|null $productId
     * @return array
     */
    public function getRenderImageList(string $perspective, State $state, array $imageList, string $productId = null): array;
}