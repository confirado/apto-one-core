<?php
namespace Apto\Plugins\PartsList\Application\Frontend\Service\BasketItem;

use Apto\Base\Domain\Core\Service\AptoParameterInterface;
use Money\Currency;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Application\Core\Query\CustomerGroup\CustomerGroupFinder;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\CircularReferenceException;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\ComputedProductValueCalculator;
use Apto\Catalog\Application\Core\Service\Formula\NoProductIdGivenException;
use Apto\Catalog\Application\Core\Service\Formula\NoStateGivenException;
use Apto\Catalog\Application\Frontend\Service\BasketItemDataProvider;
use Apto\Catalog\Domain\Core\Model\Configuration\BasketConfiguration;
use Apto\Plugins\PartsList\Domain\Core\Service\ConfigurationPartsList;

class PartsListBasketItemDataProvider implements BasketItemDataProvider
{
    /**
     * @var ConfigurationPartsList
     */
    private $configurationPartsList;

    /**
     * @var ComputedProductValueCalculator
     */
    private $computedProductValueCalculator;

    /**
     * @var CustomerGroupFinder
     */
    protected $customerGroupFinder;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @param AptoParameterInterface $aptoParameter
     * @param ConfigurationPartsList $configurationPartsList
     * @param ComputedProductValueCalculator $computedProductValueCalculator
     * @param CustomerGroupFinder $customerGroupFinder
     */
    public function __construct(
        AptoParameterInterface $aptoParameter,
        ConfigurationPartsList $configurationPartsList,
        ComputedProductValueCalculator $computedProductValueCalculator,
        CustomerGroupFinder $customerGroupFinder
    ) {
        $this->configurationPartsList = $configurationPartsList;
        $this->computedProductValueCalculator = $computedProductValueCalculator;
        $this->customerGroupFinder = $customerGroupFinder;

        // set parameters
        $this->parameters = [
            'add_list_to_basket_item' => false
        ];

        if ($aptoParameter->has('apto_plugin_parts_list')) {
            $this->parameters = $aptoParameter->get('apto_plugin_parts_list');
        }
    }

    /***
     * @param array $data
     * @param AptoUuid $shopId
     * @param BasketConfiguration $basketConfiguration
     * @param AptoLocale $locale
     * @param Currency $currency
     * @param Currency $fallbackCurrency
     * @return array
     * @throws CircularReferenceException
     * @throws InvalidUuidException
     * @throws NoProductIdGivenException
     * @throws NoStateGivenException
     */
    public function getData(
        array $data,
        AptoUuid $shopId,
        BasketConfiguration $basketConfiguration,
        AptoLocale $locale,
        Currency $currency,
        Currency $fallbackCurrency
    ): array {
        if (false === $this->parameters['add_list_to_basket_item']) {
            return $data;
        }

        $partsList = [];
        $productId = $basketConfiguration->getProduct()->getId();
        $state = $basketConfiguration->getState();

        $computedValues = $this->computedProductValueCalculator->calculateComputedValues($productId, $state, true);
        $customerGroups = $this->customerGroupFinder->findAllExternalAndUuidsByShopId($shopId->getId());
        $fallbackCustomerGroup = $this->customerGroupFinder->findFallbackCustomerGroup();

        foreach ($customerGroups as $customerGroup) {
            $groupExternalId = $customerGroup['externalId'];
            $partsList[$groupExternalId] = $this->configurationPartsList->getBasicList(
                $productId,
                $state,
                $currency,
                $customerGroup['id'],
                $fallbackCustomerGroup === null ? $fallbackCustomerGroup : $fallbackCustomerGroup['id'],
                $locale,
                $computedValues
            );
        }

        $data['apto-plugin-parts-list'] = $partsList;
        return $data;
    }
}
