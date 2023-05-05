<?php

namespace Apto\Catalog\Application\Core\Service\RenderImage;

use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

interface RenderImageProvider
{
    /**
     * @param string $perspective
     * @param State $state
     * @param string|null $productId
     * @return array
     */
    public function getRenderImageList(string $perspective, State $state, string $productId = null): array;
}