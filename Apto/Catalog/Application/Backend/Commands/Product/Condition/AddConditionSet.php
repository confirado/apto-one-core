<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Condition;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class AddConditionSet extends ProductChildCommand
{
    /**
     * @var string
     */
    private string $identifier;

    /**
     * @param string $productId
     * @param string $identifier
     */
    public function __construct(string $productId, string $identifier)
    {
        parent::__construct($productId);
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
