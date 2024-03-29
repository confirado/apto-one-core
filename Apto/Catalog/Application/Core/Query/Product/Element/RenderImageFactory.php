<?php

namespace Apto\Catalog\Application\Core\Query\Product\Element;

use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

interface RenderImageFactory
{
    public function getRenderImagesByImageList(State $state, string $productId = null): array;
}
