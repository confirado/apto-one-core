<?php
namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Material;

use Apto\Base\Application\Core\CommandInterface;

class AddMaterialPrice implements CommandInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var mixed
     */
    private $amount;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $customerGroupId;

    /**
     * @ todo define accepted type for amount
     * AddProductPrice constructor.
     * @param string $id
     * @param mixed $amount
     * @param string $currency
     * @param string $customerGroupId
     */
    public function __construct(string $id, $amount, string $currency, string $customerGroupId)
    {
        $this->id = $id;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->customerGroupId = $customerGroupId;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getCustomerGroupId(): string
    {
        return $this->customerGroupId;
    }
}