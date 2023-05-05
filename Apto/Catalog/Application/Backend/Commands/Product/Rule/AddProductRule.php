<?php

namespace Apto\Catalog\Application\Backend\Commands\Product\Rule;

use Apto\Catalog\Application\Backend\Commands\Product\ProductChildCommand;

class AddProductRule extends ProductChildCommand
{
    /**
     * @var string
     */
    private $ruleName;

    /**
     * AddProductRule constructor.
     * @param string $productId
     * @param string $ruleName
     */
    public function __construct(string $productId, string $ruleName)
    {
        parent::__construct($productId);
        $this->ruleName = $ruleName;
    }

    /**
     * @return string
     */
    public function getRuleName(): string
    {
        return $this->ruleName;
    }
}