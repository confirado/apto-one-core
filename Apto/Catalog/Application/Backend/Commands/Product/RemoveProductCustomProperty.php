<?php
namespace Apto\Catalog\Application\Backend\Commands\Product;

class RemoveProductCustomProperty extends ProductChildCommand
{
    /**
     * @var string
     */
    private $key;

    /**
     * RemoveProductPrice constructor.
     * @param string $productId
     * @param string $key
     */
    public function __construct(string $productId,  string $key)
    {
        parent::__construct($productId);
        $this->key = $key;
    }
    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }
}