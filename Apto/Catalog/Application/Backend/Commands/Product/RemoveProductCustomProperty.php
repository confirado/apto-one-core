<?php
namespace Apto\Catalog\Application\Backend\Commands\Product;

class RemoveProductCustomProperty extends ProductChildCommand
{
    /**
     * @var string
     */
    private $id;

    /**
     * RemoveProductPrice constructor.
     * @param string $productId
     * @param string $key
     */
    public function __construct(string $productId,  string $id)
    {
        parent::__construct($productId);
        $this->id = $id;
    }
    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
