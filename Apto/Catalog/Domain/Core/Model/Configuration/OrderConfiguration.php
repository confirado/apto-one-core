<?php

namespace Apto\Catalog\Domain\Core\Model\Configuration;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\Customer\Customer;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Product;

class OrderConfiguration extends Configuration
{
    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var string
     */
    protected $name;

    /**
     * OrderConfiguration constructor.
     * @param AptoUuid $id
     * @param Product $product
     * @param Customer $customer
     * @param State $state
     */
    public function __construct(AptoUuid $id, Product $product, Customer $customer, State $state)
    {
        parent::__construct($id, $product, $state);
        $this->customer = $customer;

        $this->publish(
            new OrderConfigurationAdded(
                $this->getId(),
                $product->getId(),
                $customer->getId(),
                $state
            )
        );
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return OrderConfiguration
     */
    public function setName(string $name): OrderConfiguration
    {
        if (null !== $this->name && $this->name == $name) {
            return $this;
        }

        $this->name = $name;

        $this->publish(
            new OrderConfigurationNameUpdated(
                $this->getId(),
                $this->getName()
            )
        );
        return $this;
    }

    /**
     * @param State $state
     * @return OrderConfiguration
     */
    public function setState(State $state): OrderConfiguration
    {
        if (null !== $this->state && $this->state->equals($state)) {
            return $this;
        }

        parent::setState($state);

        $this->publish(
            new OrderConfigurationStateUpdated(
                $this->getId(),
                $this->getState()
            )
        );
        return $this;
    }
}