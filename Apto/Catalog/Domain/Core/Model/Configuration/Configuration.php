<?php

namespace Apto\Catalog\Domain\Core\Model\Configuration;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoCustomProperties;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Product;
use Doctrine\Common\Collections\ArrayCollection;

class Configuration extends AptoAggregate implements ConfigurationInterface
{
    use AptoCustomProperties;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var State
     */
    protected $state;

    /**
     * Configuration constructor.
     * @param AptoUuid $id
     * @param Product $product
     * @param State $state
     */
    public function __construct(AptoUuid $id, Product $product, State $state)
    {
        parent::__construct($id);
        $this->product = $product;
        $this->state = $state;
        $this->customProperties = new ArrayCollection();
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @param State $state
     * @return $this
     */
    public function setProductAndState(Product $product, State $state): self
    {
        $this->product = $product;
        $this->setState($state);
        return $this;
    }

    /**
     * @return State
     */
    public function getState(): State
    {
        return $this->state;
    }

    /**
     * @param State $state
     */
    public function setState(State $state)
    {
        $this->state = $state;
    }

    /**
     * @param array $a
     * @param array $b
     * @return int
     */
    public static function sortRenderImagesByLayer($a, $b)
    {
        if ($a['layer'] == $b['layer']) {
            return 0;
        }
        return ($a['layer'] < $b['layer']) ? -1 : 1;
    }
}
