<?php

namespace Apto\Catalog\Application\Backend\Service\Product;

use Apto\Base\Domain\Core\Model\AptoUuid;

interface ProductPluginCopyProvider
{
    /**
     * @param AptoUuid $oldProductId
     * @param AptoUuid $productId
     * @param array $entityMapping
     * @return void
     */
    public function copy(AptoUuid $oldProductId, AptoUuid $productId, array $entityMapping): void;
}