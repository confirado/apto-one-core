<?php
namespace Apto\Catalog\Application\Backend\Commands\Product;

use Apto\Base\Application\Core\CommandInterface;

class AddProductDiscount implements CommandInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var float
     */
    private $discount;

    /**
     * @var string
     */
    private $customerGroupId;

    /**
     * @var array
     */
    private $name;

    /**
     * AddProductDiscount constructor.
     * @param string $id
     * @param float $discount
     * @param string $customerGroupId
     * @param array $name
     */
    public function __construct(string $id, float $discount, string $customerGroupId, array $name)
    {
        $this->id = $id;
        $this->discount = $discount;
        $this->customerGroupId = $customerGroupId;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getDiscount(): float
    {
        return $this->discount;
    }

    /**
     * @return string
     */
    public function getCustomerGroupId(): string
    {
        return $this->customerGroupId;
    }

    /**
     * @return array
     */
    public function getName(): array
    {
        return $this->name;
    }
}