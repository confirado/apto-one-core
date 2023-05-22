<?php

namespace Apto\Catalog\Application\Frontend\Commands\Configuration;

class AddCustomerConfiguration extends AbstractAddBasketConfiguration
{
    /**
     * @var array
     */
    private array $customer;

    /**
     * @var string
     */
    private string $name;

    /**
     * @param string $productId
     * @param array $state
     * @param array $sessionCookies
     * @param string $locale
     * @param int|null $quantity
     * @param array $perspectives
     * @param array $additionalData
     */
    public function __construct(string $productId, array $state, array $sessionCookies, string $locale, int $quantity = null, array $perspectives = ['persp1'], array $additionalData = [])
    {
        parent::__construct($productId, $state, $sessionCookies, $locale, $quantity, $perspectives, $additionalData);
        $this->customer = [];
        $this->name = '';
    }

    /**
     * @return array
     */
    public function getCustomer(): array
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
}
