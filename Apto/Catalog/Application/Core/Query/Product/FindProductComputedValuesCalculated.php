<?php

namespace Apto\Catalog\Application\Core\Query\Product;

class FindProductComputedValuesCalculated extends AbstractFindProduct
{
    /**
     * @var array
     */
    private $state;

    /**
     * @param string $id
     * @param array $state
     */
    public function __construct(string $id, array $state)
    {
        parent::__construct($id);
        $this->state = $state;
    }

    /**
     * @return array
     */
    public function getState(): array
    {
        return $this->state;
    }
}