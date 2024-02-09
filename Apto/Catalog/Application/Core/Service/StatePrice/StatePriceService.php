<?php

namespace Apto\Catalog\Application\Core\Service\StatePrice;

use Apto\Base\Application\Core\Query\CustomerGroup\CustomerGroupFinder;
use Apto\Base\Application\Core\Service\RequestStore;
use Apto\Base\Application\Core\Service\ShopConnector\ConnectorConfig;
use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Catalog\Application\Core\Query\Product\ProductFinder;
use Apto\Catalog\Application\Core\Query\Shop\ShopFinder;
use Apto\Catalog\Application\Core\Service\PriceCalculator\StatePriceCalculator;
use Apto\Catalog\Application\Core\Service\ShopConnector\TaxStateConnector;
use Apto\Catalog\Application\Core\Service\TaxCalculator\SimpleTaxCalculator;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Formatter\IntlMoneyFormatter;
use Apto\Base\Domain\Core\Model\InvalidUuidException;

class StatePriceService
{
    /**
     * @var array
     */
    protected static $taxStates;

    /**
     * @var ShopFinder
     */
    protected $shopFinder;

    /**
     * @var ProductFinder
     */
    private $productFinder;

    /**
     * @var CustomerGroupFinder
     */
    private $customerGroupFinder;

    /**
     * @var TaxStateConnector
     */
    private $taxStateConnector;

    /**
     * @var RequestStore
     */
    protected $requestStore;

    /**
     * @var StatePriceCalculator
     */
    private $statePriceCalculator;

    /**
     * @var ISOCurrencies
     */
    private $currencies;

    /**
     * StatePriceService constructor.
     * @param ShopFinder $shopFinder
     * @param ProductFinder $productFinder
     * @param CustomerGroupFinder $customerGroupFinder
     * @param TaxStateConnector $taxStateConnector
     * @param RequestStore $requestStore
     * @param StatePriceCalculator $statePriceCalculator
     */
    public function __construct(
        ShopFinder $shopFinder,
        ProductFinder $productFinder,
        CustomerGroupFinder $customerGroupFinder,
        TaxStateConnector $taxStateConnector,
        RequestStore $requestStore,
        StatePriceCalculator $statePriceCalculator
    ) {
        $this->shopFinder = $shopFinder;
        $this->productFinder = $productFinder;
        $this->customerGroupFinder = $customerGroupFinder;
        $this->taxStateConnector = $taxStateConnector;
        $this->requestStore = $requestStore;
        $this->statePriceCalculator = $statePriceCalculator;

        $this->currencies = new ISOCurrencies();
        self::$taxStates = [];
    }

    /**
     * @param AptoUuid $productId
     * @param State $state
     * @param AptoLocale $locale
     * @param array $shopCurrency
     * @param array $displayCurrency
     * @param string|null $customerGroupExternalId
     * @param array $sessionCookies
     * @param string|null $taxState
     * @param array|null $user
     * @return array
     * @throws InvalidUuidException
     * @throws \Exception
     */
    public function getStatePrice(
        AptoUuid $productId,
        State $state,
        AptoLocale $locale,
        array $shopCurrency,
        array $displayCurrency,
        string $customerGroupExternalId = null,
        array $sessionCookies = [],
        ?string $taxState = null,
        ?array $user = null
    ): array {
        // create shop and display currency
        $shopCurrencyObj = new Currency($shopCurrency['currency']);
        $displayCurrencyObj = new Currency($displayCurrency['currency']);

        // get shop id
        $connector = $this->shopFinder->findConnectorConfigByDomain($this->requestStore->getHttpHost());

        // get customer group id by external id or fallback
        // @todo is it save to use the customer group external id from javascript for price calculation?
        // currently we use this query only for display the price for the user while configuration, so in this case its save
        $customerGroup = $this->getCustomerGroup($connector['shopId'], $customerGroupExternalId);

        // create tax calculator
        $taxCalculator = new SimpleTaxCalculator(
            (string) $this->productFinder->findTaxRateById($productId->getId()),
            $customerGroup['inputGross'],
            $customerGroup['showGross']
        );

        // get tax state
        try {
            $connectorConfig = $this->getConnectorConfig($connector, $locale, $sessionCookies);
            if ($connectorConfig !== null && $taxState === null) {

                if (array_key_exists($connectorConfig->getUrl(), self::$taxStates)) {
                    $taxState = self::$taxStates[$connectorConfig->getUrl()];
                } else {
                    $taxState = $this->taxStateConnector->findTaxState($connectorConfig);
                    self::$taxStates[$connectorConfig->getUrl()] = $taxState;
                }

                if (
                    array_key_exists('result', $taxState) &&
                    is_array($taxState['result']) &&
                    array_key_exists('taxState', $taxState['result']) &&
                    trim($taxState['result']['taxState']) === 'tax-free'
                ) {
                    $taxCalculator->setTaxFree(true);
                }
            } elseif ($taxState === 'tax-free') {
                $taxCalculator->setTaxFree(true);
            }
        } catch (\Exception $exception) {}

        // get prices by state
        $prices = $this->statePriceCalculator->getDisplayPrices(
            new AptoUuid($productId->getId()),
            $customerGroup,
            $displayCurrencyObj,
            $state,
            $taxCalculator,
            new Currency($connector['currency']), // @todo set fallback currency
            $displayCurrency['factor'] ? $displayCurrency['factor'] : 1,
            $user
        );

        // @todo currency conversion was done before tax apply, tax apply is now done inside price calculator
        // tax apply would now be done before currency conversion
        // we need a new approach for currency conversion now
        // for now we throw an exception when currency conversion is needed
        if (!$displayCurrencyObj->equals($shopCurrencyObj)) {
            throw new \Exception('Currency conversion is not supported yet!');
            //$price = $this->convertCurrency($price, $displayCurrencyObj, $displayCurrency['factor']);
        }

        // @ todo maybe we need a formatter service here
        $moneyFormatter = new DecimalMoneyFormatter($this->currencies);
        $numberFormatter = new \NumberFormatter($locale->getName(), \NumberFormatter::CURRENCY);
        $intlMoneyFormatter = new IntlMoneyFormatter($numberFormatter, $this->currencies);

        return $this->formatPrices($prices, $displayCurrencyObj, $moneyFormatter, $intlMoneyFormatter);
    }

    /**
     * @param array $connectorData
     * @param AptoLocale $locale
     * @param array $sessionCookies
     * @return ConnectorConfig|null
     */
    protected function getConnectorConfig(array $connectorData, AptoLocale $locale, array $sessionCookies = []): ?ConnectorConfig
    {
        // build connector configuration
        if (
            !array_key_exists('connectorUrl', $connectorData) ||
            !array_key_exists('connectorToken', $connectorData) ||
            !$connectorData['connectorUrl'] ||
            !$connectorData['connectorToken']
        ) {
            // we habe nothing to do here because no connector is configured
            return null;
        }

        if (is_array($connectorData['connectorUrl'])) {
            $connectorUrl = AptoTranslatedValue::fromArray($connectorData['connectorUrl']);
            $connectorData['connectorUrl'] = $connectorUrl->getTranslation($locale)->getValue();
        }

        if (!trim($connectorData['connectorUrl'])) {
            return null;
        }

        return ConnectorConfig::fromArray($connectorData, $sessionCookies);
    }

    /**
     * @param string $shopId
     * @param string|null $customerGroupExternalId
     * @return array
     */
    protected function getCustomerGroup(string $shopId, string $customerGroupExternalId = null): array
    {
        $customerGroup = null;

        // try find customergroup for shop
        if (null !== $customerGroupExternalId) {
            $customerGroup = $this->customerGroupFinder->findByShopAndExternalId(
                $shopId,
                $customerGroupExternalId
            );
        }

        // try find default customer group
        if (null === $customerGroup) {
            $customerGroup = $this->customerGroupFinder->findById('00000000-0000-0000-0000-000000000000');
        }

        // if no customergroup was found create temp customergroup
        if (null === $customerGroup) {
            $customerGroup = [
                'id' => '00000000-0000-0000-0000-000000000000',
                'showGross' => true,
                'inputGross' => true,
                'fallback' => false
            ];
        }

        return $customerGroup;
    }

    /**
     * @param array $prices
     * @param Currency $displayCurrencyObj
     * @param DecimalMoneyFormatter $moneyFormatter
     * @param IntlMoneyFormatter $intlMoneyFormatter
     * @return array
     */
    protected function formatPrices(array &$prices, Currency $displayCurrencyObj, DecimalMoneyFormatter $moneyFormatter, IntlMoneyFormatter $intlMoneyFormatter): array
    {
        $formattedPrices = $this->formatPrice($prices, $moneyFormatter, $intlMoneyFormatter);
        $formattedPrices['currency'] = $displayCurrencyObj->getCode();
        $formattedPrices['sections'] = [];

        // format product surcharges
        $formattedPrices['productSurcharges'] = [];
        foreach ($prices['productSurcharges'] as $productSurcharge) {
            $formattedPrices['productSurcharges'][] = [
                'name' => $productSurcharge['name'],
                'amount' => floatval($moneyFormatter->format($productSurcharge['surcharge'])),
                'formatted' => $intlMoneyFormatter->format($productSurcharge['surcharge'])
            ];
        }

        // format section prices
        foreach ($prices['sections'] as $sectionId => $section) {
            foreach ($section as $repetition => $sectionItem) {
                $formattedSection = $this->formatPrice($sectionItem, $moneyFormatter, $intlMoneyFormatter);
                $formattedSection['elements'] = [];

                foreach ($sectionItem['elements'] as $elementId => $element) {
                    $formattedElement = $this->formatPrice($element, $moneyFormatter, $intlMoneyFormatter);
                    $formattedSection['elements'][$elementId] = $formattedElement;
                }

                $formattedPrices['sections'][$sectionId][$repetition] = $formattedSection;
            }
        }

        return $formattedPrices;
    }

    /**
     * @param array $price
     * @param DecimalMoneyFormatter $moneyFormatter
     * @param IntlMoneyFormatter $intlMoneyFormatter
     * @return array
     */
    protected function formatPrice(array &$price, DecimalMoneyFormatter $moneyFormatter, IntlMoneyFormatter $intlMoneyFormatter): array
    {
        $result = [
            'discount' => $price['discount']
        ];

        $types = ['own', 'sum'];
        $typePrices = ['pseudoPrice', 'pseudoDiff', 'price', 'netPrice', 'grossPrice'];

        // loop types
        foreach ($types as $type) {

            // skip type if not existing
            if (!isset($price[$type])) {
                continue;
            }

            // loop price types
            foreach ($typePrices as $typePrice) {

                // skip typePrice if not existing
                if (!isset($price[$type][$typePrice])) {
                    continue;
                }

                // set result
                $result[$type][$typePrice] = [
                    'amount' => floatval($moneyFormatter->format($price[$type][$typePrice])),
                    'formatted' => $intlMoneyFormatter->format($price[$type][$typePrice])
                ];
            }
        }

        $additionals = ['additionalInformation'];

        // loop additionals
        foreach ($additionals as $additional) {

            // skip additional if not existing
            if (isset($price[$additional])) {
                // look for paths that want to be formatted
                if (array_key_exists('formatPricePaths', $price[$additional])) {
                    foreach ($price[$additional]['formatPricePaths'] as $formatPricePath) {
                        $priceRef =& $price[$additional];

                        foreach ($formatPricePath as $key) {
                            $priceRef =& $priceRef[$key];
                        }

                        $priceRef = [
                            'amount' => floatval($moneyFormatter->format($priceRef)),
                            'formatted' => $intlMoneyFormatter->format($priceRef)
                        ];
                    }
                }

                // set result
                $result[$additional] = $price[$additional];
            }
        }

        return $result;
    }
}
