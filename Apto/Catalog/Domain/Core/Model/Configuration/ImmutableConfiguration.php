<?php

namespace Apto\Catalog\Domain\Core\Model\Configuration;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Product;
use Apto\Base\Domain\Core\Model\InvalidUuidException;

class ImmutableConfiguration extends Configuration
{
    /**
     * ImmutableConfiguration constructor.
     * @param AptoUuid $id
     * @param Product $product
     * @param State $state
     * @throws ConfigurationInvalidMultipleSelection
     * @throws ConfigurationProductRulesNotFulfilled
     * @throws ConfigurationValueNotAllowedException
     * @throws InvalidUuidException
     */
    public function __construct(AptoUuid $id, Product $product, State $state)
    {
        parent::__construct($id, $product, $state);
    }
}
