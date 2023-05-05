<?php

namespace Apto\Catalog\Application\Frontend\Commands\Configuration;

use Apto\Base\Application\Core\PublicCommandInterface;

class ConvertBasketToOrderConfiguration implements PublicCommandInterface
{
    /**
     * @var array
     */
    private $customer;

    /**
     * @var array
     */
    private $configurationIds;

    /**
     * @var string
     */
    private $locale;

    // @todo: what to do, if configuration is invalid and should be converted from basket to ordered?
    // maybe we could make a new Model ConfigurationType and on convert we simple change ConfigurationType and dont create a new entity?
    // we can than use ConfigurationType for other entities like CustomerConfiguration too

    /**
     * ConvertBasketToOrderConfiguration constructor.
     * @param array $customer
     * @param array $configurationIds
     * @param string $locale
     */
    public function __construct(array $customer, array $configurationIds, string $locale)
    {
        $this->customer = $customer;
        $this->configurationIds = $configurationIds;
        $this->locale = $locale;
    }

    /**
     * @return array
     */
    public function getCustomer(): array
    {
        return $this->customer;
    }

    /**
     * @return array
     */
    public function getConfigurationIds(): array
    {
        return $this->configurationIds;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }
}