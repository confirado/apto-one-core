<?php

namespace Apto\Catalog\Domain\Core\Model\Configuration;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Product;

class SharedConfiguration extends Configuration
{
    /**
     * SharedConfiguration constructor.
     * @param AptoUuid $id
     * @param Product $product
     * @param State $state
     */
    public function __construct(AptoUuid $id, Product $product, State $state)
    {
        parent::__construct($id, $product, $state);
    }
}
