<?php

namespace Apto\Catalog\Domain\Core\Model\Configuration;

use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Product;

interface ConfigurationInterface
{
    /**
     * @return Product
     */
    public function getProduct(): Product;

    /**
     * @param Product $product
     * @param State $state
     * @return mixed
     */
    public function setProductAndState(Product $product, State $state);

    /**
     * @return State
     */
    public function getState(): State;

    /**
     * @param State $state
     * @return mixed
     */
    public function setState(State $state);

    /**
     * @param array $a
     * @param array $b
     * @return int
     */
    public static function sortRenderImagesByLayer($a, $b);
}
