<?php

namespace Apto\Catalog\Application\Core\Query\Configuration;

use Apto\Base\Application\Core\PublicQueryInterface;

class FindHumanReadableState implements PublicQueryInterface
{
    /**
     * @var string
     */
    private $productId;

    /**
     * @var array
     */
    private $state;

    /**
     * FindHumanReadableStateByState constructor.
     * @param string $productId
     * @param array $state
     */
    public function __construct(string $productId, array $state)
    {
        $this->productId = $productId;
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }

    /**
     * @return array
     */
    public function getState(): array
    {
        return $this->state;
    }
}