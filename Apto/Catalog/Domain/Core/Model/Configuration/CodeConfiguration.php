<?php

namespace Apto\Catalog\Domain\Core\Model\Configuration;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Product;

class CodeConfiguration extends Configuration
{
    /**
     * @var string
     */
    protected $code;

    /**
     * CodeConfiguration constructor.
     * @param AptoUuid $id
     * @param Product $product
     * @param State $state
     * @param string $code
     */
    public function __construct(AptoUuid $id, Product $product, State $state, string $code)
    {
        parent::__construct($id, $product, $state);
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }
}