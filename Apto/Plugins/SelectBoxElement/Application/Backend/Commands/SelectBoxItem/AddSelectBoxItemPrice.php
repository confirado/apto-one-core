<?php

namespace Apto\Plugins\SelectBoxElement\Application\Backend\Commands\SelectBoxItem;

use Apto\Base\Application\Core\CommandInterface;

class AddSelectBoxItemPrice implements CommandInterface
{
    /**
     * @var string
     */
    private $selectBoxItemId;

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
     * AddSelectBoxItemPrice constructor.
     * @param string $selectBoxItemId
     * @todo define accepted type for amount
     * @param $amount
     * @param string $currency
     * @param string $customerGroupId
     */
    public function __construct(string $selectBoxItemId, $amount, string $currency, string $customerGroupId)
    {
        $this->selectBoxItemId = $selectBoxItemId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->customerGroupId = $customerGroupId;
    }

    /**
     * @return string
     */
    public function getSelectBoxItemId(): string
    {
        return $this->selectBoxItemId;
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