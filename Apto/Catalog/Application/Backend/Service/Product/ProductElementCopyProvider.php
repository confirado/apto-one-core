<?php

namespace Apto\Catalog\Application\Backend\Service\Product;

use Apto\Base\Domain\Core\Model\AptoUuid;

interface ProductElementCopyProvider
{
    /**
     * @param AptoUuid $oldElementId
     * @param AptoUuid $productId
     * @param AptoUuid $sectionId
     * @param AptoUuid $elementId
     */
    public function copy(AptoUuid $oldElementId, AptoUuid $productId, AptoUuid $sectionId, AptoUuid $elementId): void;

    /**
     * @return string
     */
    public function getType(): string;
}