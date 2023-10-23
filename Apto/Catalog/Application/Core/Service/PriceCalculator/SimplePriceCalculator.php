<?php

namespace Apto\Catalog\Application\Core\Service\PriceCalculator;

use Apto\Base\Application\Core\Query\CustomerGroup\CustomerGroupFinder;
use Apto\Base\Application\Core\Service\RequestStore;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Service\AptoJsonSerializer;
use Apto\Catalog\Application\Core\Query\PriceMatrix\PriceMatrixFinder;
use Apto\Catalog\Application\Core\Query\Product\ProductFinder;
use Apto\Catalog\Application\Core\Query\Shop\ShopFinder;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\ComputedProductValueCalculator;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider\AdditionalPriceInformationProvider;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider\BasePriceProvider;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider\ElementPriceProvider;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider\ProductPriceProvider;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider\ProductSurchargeProvider;
use Apto\Catalog\Application\Core\Service\TaxCalculator\TaxCalculator;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Service\Formula\Exception\FormulaParserException;
use Apto\Catalog\Domain\Core\Service\Formula\FormulaParser;
use Money\Currency;
use Money\Money;
use Money\Converter;
use Money\Currencies\ISOCurrencies;
use Money\Exchange\FixedExchange;

use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Core\Service\AptoJsonSerializerException;

class SimplePriceCalculator implements PriceCalculator
{
    /**
     * @var PriceCalculatorRegistry
     */
    protected $priceCalculatorRegistry;

    /**
     * @var ProductFinder
     */
    protected $productFinder;

    /**
     * @var CustomerGroupFinder
     */
    protected $customerGroupFinder;

    /**
     * @var PriceMatrixFinder
     */
    protected $priceMatrixFinder;

    /**
     * @var AptoJsonSerializer
     */
    protected $aptoJsonSerializer;

    /**
     * @var array
     */
    protected $priceTable;

    /**
     * @var AptoUuid
     */
    protected $productId;

    /**
     * @var State
     */
    protected $state;

    /**
     * @var Currency|null
     */
    protected $currency;

    /**
     * @var array
     */
    protected $customerGroup;

    /**
     * @var array|null
     */
    protected $fallbackCustomerGroupOrNull;

    /**
     * @var TaxCalculator|null
     */
    protected $taxCalculator;

    /**
     * @var bool
     */
    protected $displayPrices;

    /**
     * @var Currency
     */
    protected $fallbackCurrency;

    /**
     * @var float
     */
    protected $currencyFactor;

    /**
     * @var ISOCurrencies
     */
    private $currencies;

    /**
     * @var array
     */
    private $elementIdIdentifierMapping;

    /**
     * @var ComputedProductValueCalculator
     */
    protected $computedProductValueCalculator;

    /**
     * @var array
     */
    private $computedValues;

    /**
     * @var MediaFileSystemConnector
     */
    private $mediaFileSystem;

    /**
     * @var ShopFinder
     */
    private $shopFinder;

    /**
     * @var RequestStore
     */
    private $requestStore;

    /**
     * @var float
     */
    private $priceModifier;

    /**
     * @param PriceCalculatorRegistry $priceCalculatorRegistry
     * @param ProductFinder $productFinder
     * @param CustomerGroupFinder $customerGroupFinder
     * @param PriceMatrixFinder $priceMatrixFinder
     * @param AptoJsonSerializer $aptoJsonSerializer
     * @param ComputedProductValueCalculator $computedProductValueCalculator
     * @param MediaFileSystemConnector $mediaFileSystem
     */
    public function __construct(
        PriceCalculatorRegistry $priceCalculatorRegistry,
        ProductFinder $productFinder,
        CustomerGroupFinder $customerGroupFinder,
        PriceMatrixFinder $priceMatrixFinder,
        AptoJsonSerializer $aptoJsonSerializer,
        ComputedProductValueCalculator $computedProductValueCalculator,
        MediaFileSystemConnector $mediaFileSystem,
        ShopFinder $shopFinder,
        RequestStore $requestStore
    ) {
        $this->priceCalculatorRegistry = $priceCalculatorRegistry;
        $this->productFinder = $productFinder;
        $this->customerGroupFinder = $customerGroupFinder;
        $this->priceMatrixFinder = $priceMatrixFinder;
        $this->aptoJsonSerializer = $aptoJsonSerializer;
        $this->taxCalculator = null;
        $this->displayPrices = true;
        $this->currency = null;
        $this->fallbackCustomerGroupOrNull = null;
        $this->fallbackCurrency = new Currency('EUR');
        $this->currencyFactor = 1.0;
        $this->currencies = new ISOCurrencies();
        $this->elementIdIdentifierMapping = [];
        $this->computedProductValueCalculator = $computedProductValueCalculator;
        $this->computedValues = [];
        $this->mediaFileSystem = $mediaFileSystem;
        $this->shopFinder = $shopFinder;
        $this->requestStore = $requestStore;
        $this->priceModifier = 1;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return static::class;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'SimplePriceCalculator';
    }

    /**
     * @return AptoUuid
     */
    public function getProductId(): AptoUuid
    {
        return $this->productId;
    }

    /**
     * @return State
     */
    public function getState(): State
    {
        return $this->state;
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @return Currency
     */
    public function getFallbackCurrency(): Currency
    {
        return $this->fallbackCurrency;
    }

    /**
     * @return array
     */
    public function getCustomerGroup(): array
    {
        return $this->customerGroup;
    }

    /**
     * @return array|null
     */
    public function getFallbackCustomerGroupOrNull()
    {
        return $this->fallbackCustomerGroupOrNull;
    }

    /**
     * @return TaxCalculator|null
     */
    public function getTaxCalculator(): ?TaxCalculator
    {
        return $this->taxCalculator;
    }

    /**
     * @return array
     */
    public function getElementIdIdentifierMapping(): array
    {
        return $this->elementIdIdentifierMapping;
    }

    /**
     * @return array
     */
    public function getComputedValues(): array
    {
        return $this->computedValues;
    }

    /**
     * @param array $prices
     * @return Money
     */
    public function getTaxAdaptedPriceByPreferredCustomerGroup(array $prices): Money
    {
        // lookup customer group id and currency
        $customerGroupId = $this->customerGroup['id'];
        foreach ($prices as $price) {
            if ($price['customerGroupId'] === $customerGroupId && $price['currencyCode'] === $this->currency->getCode()) {
                return new Money($price['amount'], new Currency($price['currencyCode']));
            }
        }

        // lookup fallback customer group id (if defined) and currency
        if (null !== $this->fallbackCustomerGroupOrNull) {
            $fallbackCustomerGroupId = $this->fallbackCustomerGroupOrNull['id'];
            foreach ($prices as $price) {
                if ($price['customerGroupId'] === $fallbackCustomerGroupId && $price['currencyCode'] === $this->currency->getCode()) {
                    return $this->getTaxAdaptedPrice(
                        new Money($price['amount'], new Currency($price['currencyCode']))
                    );
                }
            }
        }

        // lookup customer group id and fallback currency
        $customerGroupId = $this->customerGroup['id'];
        foreach ($prices as $price) {
            if ($price['customerGroupId'] === $customerGroupId && $price['currencyCode'] === $this->fallbackCurrency->getCode()) {
                // convert price from fallback currency to currency
                return $this->convertCurrency(
                    new Money($price['amount'], new Currency($price['currencyCode'])),
                    $this->getCurrency(),
                    $this->currencyFactor
                );
            }
        }

        // lookup fallback customer group id (if defined) and fallback currency
        if (null !== $this->fallbackCustomerGroupOrNull) {
            $fallbackCustomerGroupId = $this->fallbackCustomerGroupOrNull['id'];
            foreach ($prices as $price) {
                if ($price['customerGroupId'] === $fallbackCustomerGroupId && $price['currencyCode'] === $this->fallbackCurrency->getCode()) {
                    // convert price from fallback currency to currency
                    return $this->convertCurrency(
                        $this->getTaxAdaptedPrice(
                            new Money($price['amount'], new Currency($price['currencyCode']))
                        ),
                        $this->getCurrency(),
                        $this->currencyFactor
                    );
                }
            }
        }

        return new Money(0, $this->getCurrency());
    }

    /**
     * @param AptoUuid $productId
     * @param array $customerGroup
     * @param Currency $currency
     * @param State $state
     * @param TaxCalculator $taxCalculator
     * @param Currency|null $fallbackCurrency
     * @param float $currencyFactor
     * @return array
     * @throws AptoJsonSerializerException
     * @throws InvalidUuidException
     */
    public function getRawPrices(
        AptoUuid $productId,
        array $customerGroup,
        Currency $currency,
        State $state,
        TaxCalculator $taxCalculator,
        Currency $fallbackCurrency = null,
        float $currencyFactor = 1.0
    ): array {
        $this->displayPrices = false;
        return $this->getCalculatedPrice(
            $state,
            $currency,
            $taxCalculator,
            $customerGroup,
            $productId,
            $fallbackCurrency,
            $currencyFactor
        );
    }

    /**
     * @param AptoUuid $productId
     * @param array $customerGroup
     * @param Currency $currency
     * @param State $state
     * @param TaxCalculator $taxCalculator
     * @param Currency|null $fallbackCurrency
     * @param float $currencyFactor
     * @return array
     * @throws AptoJsonSerializerException
     * @throws InvalidUuidException
     */
    public function getDisplayPrices(
        AptoUuid $productId,
        array $customerGroup,
        Currency $currency,
        State $state,
        TaxCalculator $taxCalculator,
        Currency $fallbackCurrency = null,
        float $currencyFactor = 1.0
    ): array {
        $this->displayPrices = true;
        return $this->getCalculatedPrice(
            $state,
            $currency,
            $taxCalculator,
            $customerGroup,
            $productId,
            $fallbackCurrency,
            $currencyFactor
        );
    }

    /**
     * @param Money $fallBackCustomerGroupPrice
     * @return Money
     */
    protected function getTaxAdaptedPrice(Money $fallBackCustomerGroupPrice): Money
    {
        if (null === $this->fallbackCustomerGroupOrNull) {
            throw new \InvalidArgumentException('This method must only be used with a fallbackCustomerGroup set.');
        }

        $cGross = $this->customerGroup['inputGross'];
        $fcGross = $this->fallbackCustomerGroupOrNull['inputGross'];

        if ($cGross && !$fcGross) {
            return $this->taxCalculator->addTax($fallBackCustomerGroupPrice);
        }

        if (!$cGross && $fcGross) {
            return $this->taxCalculator->subTax($fallBackCustomerGroupPrice);
        }

        // $cGross === $fcGross, no conversion needed due to same tax settings on both customer groups
        return $fallBackCustomerGroupPrice;
    }

    /**
     * @param Money $taxAdaptedElementPrice
     * @param array $priceMatrix
     * @return Money
     */
    protected function applyPriceMatrix(Money $taxAdaptedElementPrice, array $priceMatrix): Money
    {
        // assert activated price matrix
        if (count($priceMatrix) === 0 || !$priceMatrix['priceMatrixActive']) {
            return $taxAdaptedElementPrice;
        }

        // try to calculate row and column
        $values = array_merge($this->computedValues, [
            '_anzahl_' => $this->state->getParameter(State::QUANTITY)
        ]);
        try {
            $row = FormulaParser::parse($priceMatrix['priceMatrixRow'], $values, $this->mediaFileSystem);
            $column = FormulaParser::parse($priceMatrix['priceMatrixColumn'], $values, $this->mediaFileSystem);
        }
        catch (FormulaParserException $e) {
            return $taxAdaptedElementPrice;
        }

        // get price data
        $priceMatrixElementPrices = $this->priceMatrixFinder->findNextHigherPriceByColumnRowValue(
            $priceMatrix['priceMatrixId'],
            floatval($row),
            floatval($column),
            $this->customerGroup['id'],
            $this->fallbackCustomerGroupOrNull ? $this->fallbackCustomerGroupOrNull['id'] : null,
            $this->currency->getCode(),
            $this->fallbackCurrency->getCode()
        );

        // add preferred price
        return $taxAdaptedElementPrice->add(
            $this->getTaxAdaptedPriceByPreferredCustomerGroup($priceMatrixElementPrices)
        );
    }

    /**
     * @param Money $taxAdaptedElementPrice
     * @param array $priceFormulas
     * @return Money
     */
    protected function applyPriceFormulaByPreferredCustomerGroup(Money $taxAdaptedElementPrice, array $priceFormulas): Money
    {
        // lookup customer group id and currency
        $customerGroupId = $this->customerGroup['id'];
        foreach ($priceFormulas as $priceFormula) {
            if ($priceFormula['customerGroupId'] === $customerGroupId && $priceFormula['currencyCode'] === $this->currency->getCode()) {
                return (new Money(
                    $this->parsePriceFormula($priceFormula['formula']),
                    new Currency($priceFormula['currencyCode'])
                ))->add($taxAdaptedElementPrice);
            }
        }

        // lookup fallback customer group id (if defined) and currency
        if (null !== $this->fallbackCustomerGroupOrNull) {
            $fallbackCustomerGroupId = $this->fallbackCustomerGroupOrNull['id'];
            foreach ($priceFormulas as $priceFormula) {
                if ($priceFormula['customerGroupId'] === $fallbackCustomerGroupId && $priceFormula['currencyCode'] === $this->currency->getCode()) {
                    return $this->getTaxAdaptedPrice(
                        new Money(
                            $this->parsePriceFormula($priceFormula['formula']),
                            new Currency($priceFormula['currencyCode'])
                        )
                    )->add($taxAdaptedElementPrice);
                }
            }
        }

        // lookup customer group id and fallback currency
        $customerGroupId = $this->customerGroup['id'];
        foreach ($priceFormulas as $priceFormula) {
            if ($priceFormula['customerGroupId'] === $customerGroupId && $priceFormula['currencyCode'] === $this->fallbackCurrency->getCode()) {
                // convert price from fallback currency to currency
                return $this->convertCurrency(
                    new Money(
                        $this->parsePriceFormula($priceFormula['formula']),
                        new Currency($priceFormula['currencyCode'])
                    ),
                    $this->getCurrency(),
                    $this->currencyFactor
                )->add($taxAdaptedElementPrice);
            }
        }

        // lookup fallback customer group id (if defined) and fallback currency
        if (null !== $this->fallbackCustomerGroupOrNull) {
            $fallbackCustomerGroupId = $this->fallbackCustomerGroupOrNull['id'];
            foreach ($priceFormulas as $priceFormula) {
                if ($priceFormula['customerGroupId'] === $fallbackCustomerGroupId && $priceFormula['currencyCode'] === $this->fallbackCurrency->getCode()) {
                    // convert price from fallback currency to currency
                    return $this->convertCurrency(
                        $this->getTaxAdaptedPrice(
                            new Money(
                                $this->parsePriceFormula($priceFormula['formula']),
                                new Currency($priceFormula['currencyCode'])
                            )
                        ),
                        $this->getCurrency(),
                        $this->currencyFactor
                    )->add($taxAdaptedElementPrice);
                }
            }
        }

        return $taxAdaptedElementPrice;
    }


    /**
     * @param string $formula
     * @return string
     */
    protected function parsePriceFormula(string $formula): string
    {
        $values = array_merge($this->computedValues, [
            '_waehrung_' => $this->currency ? $this->currency->getCode() : $this->fallbackCurrency->getCode(),
            '_anzahl_' => $this->state->getParameter(State::QUANTITY)
        ]);
        try {
            return FormulaParser::parse(
                $formula,
                $values,
                $this->mediaFileSystem
            );
        }
        catch (FormulaParserException $e) {
            return '0';
        }
    }

    /**
     * @param Money $taxAdaptedElementPrice
     * @param array $prices
     * @return Money
     */
    protected function applyExtendedPriceCalculationFormula(Money $taxAdaptedElementPrice, array $prices): Money
    {
        // calculate price by formula
        if (
            count($prices) > 0 &&
            array_key_exists('extendedPriceCalculationActive', $prices[0]) &&
            array_key_exists('extendedPriceCalculationFormula', $prices[0]) &&
            $prices[0]['extendedPriceCalculationActive']
        ) {
            $values = array_merge($this->computedValues, [
                '_preis_' => $taxAdaptedElementPrice->getAmount(),
                '_waehrung_' => $this->currency ? $this->currency->getCode() : $this->fallbackCurrency->getCode(),
                '_anzahl_' => $this->state->getParameter(State::QUANTITY)
            ]);
            try {
                $result = FormulaParser::parse(
                    $prices[0]['extendedPriceCalculationFormula'],
                    $values,
                    $this->mediaFileSystem
                );

                return new Money(
                    (string) round(floatval($result)),
                    $taxAdaptedElementPrice->getCurrency()
                );
            }
            catch (FormulaParserException $e) {
                return $taxAdaptedElementPrice;
            }
        }

        // return default price
        return $taxAdaptedElementPrice;
    }

    /**
     * @param array $array
     * @param string $key
     * @param bool $multiple
     * @return array
     */
    protected function mapPropertyToKey(array $array, string $key, bool $multiple = false): array
    {
        $result = [];
        foreach ($array as $item) {
            if (true === $multiple) {
                $result[$item[$key]][] = $item;
            } else {
                $result[$item[$key]] = $item;
            }
        }
        return $result;
    }

    /**
     * @param array $array
     * @param array $properties
     * @param bool $multiple
     * @return array
     */
    protected function mapProperties(array $array, array $properties, bool $multiple = false): array
    {
        $result = [];
        foreach ($properties as $subArray => $key) {
            if (isset($array[$subArray])) {
                $result[$subArray] = $this->mapPropertyToKey($array[$subArray], $key, $multiple);
            } else {
                if (true === $multiple) {
                    $result[$subArray][] = $array[$subArray];
                } else {
                    $result[$subArray] = $array[$subArray];
                }
            }
        }

        return $result;
    }

    /**
     * @param array $statePrices
     * @param array $types
     */
    protected function calculateBasePriceSurcharge(array $statePrices, array $types)
    {
        $surcharge = 1;

        foreach ($types as $type) {
            foreach ($statePrices['percentageSurcharges'][$type] as $item) {
                $surcharge *= 1 + $item['percentageSurcharge'] / 100;
            }
        }

        // set result
        $this->priceTable['base']['surcharge'] = $surcharge;
    }

    /**
     * @param array $statePrices
     * @throws AptoJsonSerializerException
     * @throws InvalidUuidException
     */
    protected function calculateBasePrice(array $statePrices)
    {
        $sections = $this->state->getStateWithoutParameters();

        $prices = $statePrices['prices'];
        $priceMatrices = $statePrices['priceMatrices'];
        $priceFormulas = $statePrices['priceFormulas'];
        $discounts = $statePrices['discounts'];
        $definitions = $statePrices['definitions'];

        // for all sections
        $productSum = new Money(0, $this->currency);
        foreach ($sections as $sectionId => $elements) {
            $sectionUuid = new AptoUuid($sectionId);

            // create section subarray, if not existing
            if (!isset($this->priceTable['sections'][$sectionId])) {
                $this->priceTable['sections'][$sectionId] = [
                    'elements' => [],
                    'own' => [],
                    'sum' => []
                ];
            }

            // for all elements
            $sectionSum = new Money(0, $this->currency);
            foreach ($elements as $elementId => $properties) {
                $elementUuid = new AptoUuid($elementId);

                /** @var ElementDefinition $elementDefinition */
                $elementDefinition = $this->aptoJsonSerializer->jsonUnSerialize($definitions[$elementId]['definition']);
                $elementPriceProvider = $this->priceCalculatorRegistry->getPriceProvider(get_class($elementDefinition));
                $taxAdaptedElementPrice = $this->getTaxAdaptedPriceByPreferredCustomerGroup($prices['elements'][$elementId] ?? []);
                $taxAdaptedElementPrice = $this->applyPriceMatrix($taxAdaptedElementPrice, $priceMatrices['elements'][$elementId] ?? []);
                $taxAdaptedElementPrice = $this->applyPriceFormulaByPreferredCustomerGroup($taxAdaptedElementPrice, $priceFormulas['elements'][$elementId] ?? []);
                $taxAdaptedElementPrice = $this->applyExtendedPriceCalculationFormula($taxAdaptedElementPrice, $prices['elements'][$elementId] ?? []);

                // skip non matching providers
                if (!$elementPriceProvider instanceof BasePriceProvider) {
                    continue;
                }

                // get element pseudo price
                /** @var BasePriceProvider $elementPriceProvider */
                $elementPseudoPrice = $elementPriceProvider->getPrice(
                    $this,
                    $sectionUuid,
                    $elementUuid,
                    $elementDefinition,
                    $taxAdaptedElementPrice
                );

                // apply tax to element pseudo price
                if (true === $this->displayPrices) {
                    $elementPseudoPrice = $this->taxCalculator->getDisplayPrice($elementPseudoPrice);
                }

                // apply surcharge to element pseudo price
                $elementSurcharge = $this->priceTable['base']['surcharge'] ?? 0;
                $elementPseudoPrice = $elementPseudoPrice->multiply($elementSurcharge);

                // get element discount
                $elementDiscount = $discounts['elements'][$elementId]['discount'] ?? 0;
                $elementDiscountName = $discounts['elements'][$elementId]['name'] ?? null;
                $elementDiscountDescription = $discounts['elements'][$elementId]['description'] ?? null;

                // calculate element price
                $elementPrice = $elementPseudoPrice->multiply(1 - $elementDiscount / 100);

                // set element result
                $this->priceTable['sections'][$sectionId]['elements'][$elementId]['discount'] = [
                    'discount' => $elementDiscount,
                    'name' => $elementDiscountName,
                    'description' => $elementDiscountDescription
                ];
                $this->priceTable['sections'][$sectionId]['elements'][$elementId]['own'] = [
                    'pseudoPrice' => $elementPseudoPrice,
                    'pseudoDiff' => $elementPrice->subtract($elementPseudoPrice),
                    'price' => $elementPrice
                ];

                // sum up section
                $sectionSum = $sectionSum->add($elementPrice);
            }

            // set result
            $this->priceTable['sections'][$sectionId]['sum']['price'] = $sectionSum;

            // sum up product
            $productSum = $productSum->add($sectionSum);
        }

        // set result
        $this->priceTable['base']['price'] = $productSum;
    }

    /**
     * @param array $statePrices
     * @throws AptoJsonSerializerException
     * @throws InvalidUuidException
     */
    protected function calculateElementPrices(array $statePrices)
    {
        $sections = $this->state->getStateWithoutParameters();
        $productId = $this->productId->getId();

        $prices = $statePrices['prices'];
        $priceMatrices = $statePrices['priceMatrices'];
        $priceFormulas = $statePrices['priceFormulas'];
        $discounts = $statePrices['discounts'];
        $definitions = $statePrices['definitions'];

        // for all sections
        $productSum = new Money(0, $this->currency);
        foreach ($sections as $sectionId => $elements) {
            $sectionUuid = new AptoUuid($sectionId);

            // create section subarray, if not existing
            if (!isset($this->priceTable['sections'][$sectionId])) {
                $this->priceTable['sections'][$sectionId] = [
                    'elements' => [],
                    'own' => [],
                    'sum' => []
                ];
            }

            // for all elements
            $sectionSum = $this->priceTable['sections'][$sectionId]['sum']['price'] ?? new Money(0, $this->currency);
            foreach ($elements as $elementId => $properties) {
                $elementUuid = new AptoUuid($elementId);

                /** @var ElementDefinition $elementDefinition */
                $elementDefinition = $this->aptoJsonSerializer->jsonUnSerialize($definitions[$elementId]['definition']);
                $elementPriceProvider = $this->priceCalculatorRegistry->getPriceProvider(get_class($elementDefinition));
                $taxAdaptedElementPrice = $this->getTaxAdaptedPriceByPreferredCustomerGroup($prices['elements'][$elementId] ?? []);
                $taxAdaptedElementPrice = $this->applyPriceMatrix($taxAdaptedElementPrice, $priceMatrices['elements'][$elementId] ?? []);
                $taxAdaptedElementPrice = $this->applyPriceFormulaByPreferredCustomerGroup($taxAdaptedElementPrice, $priceFormulas['elements'][$elementId] ?? []);
                $taxAdaptedElementPrice = $this->applyExtendedPriceCalculationFormula($taxAdaptedElementPrice, $prices['elements'][$elementId] ?? []);

                // populate additional price information, if provided
                $additionInformation = [];
                if ($elementPriceProvider instanceof AdditionalPriceInformationProvider) {
                    $additionInformation = $elementPriceProvider->getAdditionalInformation(
                        $this,
                        $sectionUuid,
                        $elementUuid,
                        $elementDefinition,
                        $taxAdaptedElementPrice,
                        $this->priceTable['base']['price']
                    );
                }
                $this->priceTable['sections'][$sectionId]['elements'][$elementId]['additionalInformation'] = $additionInformation;

                // skip non matching providers
                if (!$elementPriceProvider instanceof ElementPriceProvider) {
                    continue;
                }

                // get element pseudo price
                /** @var ElementPriceProvider $elementPriceProvider */
                $elementOwnPseudoPrice = $elementPriceProvider->getPrice(
                    $this,
                    $sectionUuid,
                    $elementUuid,
                    $elementDefinition,
                    $taxAdaptedElementPrice,
                    $this->priceTable['base']['price']
                );

                // apply tax to element pseudo price
                if (true === $this->displayPrices) {
                    $elementOwnPseudoPrice = $this->taxCalculator->getDisplayPrice($elementOwnPseudoPrice);
                }

                // get element discount
                $elementDiscount = $discounts['elements'][$elementId]['discount'] ?? 0;
                $elementDiscountName = $discounts['elements'][$elementId]['name'] ?? null;
                $elementDiscountDescription = $discounts['elements'][$elementId]['description'] ?? null;

                // calculate element own price
                $elementOwnPrice = $elementOwnPseudoPrice->multiply(1 - $elementDiscount / 100);

                // set element result
                $this->priceTable['sections'][$sectionId]['elements'][$elementId]['discount'] = [
                    'discount' => $elementDiscount,
                    'name' => $elementDiscountName,
                    'description' => $elementDiscountDescription
                ];
                $this->priceTable['sections'][$sectionId]['elements'][$elementId]['own'] = [
                    'pseudoPrice' => $elementOwnPseudoPrice,
                    'pseudoDiff' => $elementOwnPrice->subtract($elementOwnPseudoPrice),
                    'price' => $elementOwnPrice
                ];

                // sum up section
                $sectionSum = $sectionSum->add($elementOwnPrice);
            }

            // calculate section pseudo price
            $sectionOwnPseudoPrice = $this->getTaxAdaptedPriceByPreferredCustomerGroup($prices['sections'][$sectionId] ?? []);

            // get section discount
            $sectionDiscount = $discounts['sections'][$sectionId]['discount'] ?? 0;
            $sectionDiscountName = $discounts['sections'][$sectionId]['name'] ?? 0;
            $sectionDiscountDescription = $discounts['sections'][$sectionId]['description'] ?? 0;

            // apply tax factor to section own pseudo price
            if (true === $this->displayPrices) {
                $sectionOwnPseudoPrice = $this->taxCalculator->getDisplayPrice($sectionOwnPseudoPrice);
            }

            // apply section discount
            $sectionOwnPrice = $sectionOwnPseudoPrice->multiply(1 - $sectionDiscount / 100);

            // calculate section sum pseudo price and section sum price
            //$sectionSumPseudoPrice = $sectionOwnPseudoPrice->add($sectionSum);
            //$sectionSumPrice = $sectionSumPseudoPrice->multiply(1 - $sectionDiscount / 100);
            // @todo currently section discount is only applied to section own price, not section sum price, due to FE display issues
            $sectionSumPseudoPrice = $sectionOwnPseudoPrice->add($sectionSum);
            $sectionSumPrice = $sectionOwnPrice->add($sectionSum);

            // set section result
            $this->priceTable['sections'][$sectionId]['discount'] = [
                'discount' => $sectionDiscount,
                'name' => $sectionDiscountName,
                'description' => $sectionDiscountDescription
            ];
            $this->priceTable['sections'][$sectionId]['own'] = [
                'pseudoPrice' => $sectionOwnPseudoPrice,
                'pseudoDiff' => $sectionOwnPrice->subtract($sectionOwnPseudoPrice),
                'price' => $sectionOwnPrice
            ];
            $this->priceTable['sections'][$sectionId]['sum'] = [
                'pseudoPrice' => $sectionSumPseudoPrice,
                'price' => $sectionSumPrice
            ];

            // sum up product
            $productSum = $productSum->add($sectionSumPrice);
        }

        // calculate product surcharges
        $productSurchargeProviders = $this->priceCalculatorRegistry->getProductSurchargeProviders();

        $productSurcharges = [];
        foreach ($productSurchargeProviders as $productSurchargeProvider) {
            /** @var ProductSurchargeProvider $productSurchargeProvider */
            $productSurcharges = array_merge($productSurcharges, $productSurchargeProvider->getSurcharges($this));
        }

        // add product surcharges to product sums
        foreach ($productSurcharges as $productSurcharge) {
            $productSum = $productSum->add($productSurcharge['surcharge']);
        }

        // calculate product pseudo price
        $productOwnPseudoPrice = $this->getTaxAdaptedPriceByPreferredCustomerGroup($prices['products'][$productId] ?? []);
        $productPriceProviders = $this->priceCalculatorRegistry->getProductPriceProviders();
        foreach ($productPriceProviders as $productPriceProvider) {
            /** @var ProductPriceProvider $productPriceProvider */
            $productOwnPseudoPrice = $productPriceProvider->getPrice($this, $productOwnPseudoPrice);
        }

        // get product discount
        $productDiscount = $discounts['products'][$productId]['discount'] ?? 0;
        $productDiscountName = $discounts['products'][$productId]['name'] ?? null;
        $productDiscountDescription = $discounts['products'][$productId]['description'] ?? null;

        // apply tax factor to product own pseudo price
        if (true === $this->displayPrices) {
            $productOwnPseudoPrice = $this->taxCalculator->getDisplayPrice($productOwnPseudoPrice);
        }

        // apply product discount
        $productOwnPrice = $productOwnPseudoPrice->multiply(1 - $productDiscount / 100);

        // calculate product sum pseudo price and product sum price
        $productSumPseudoPrice = $productOwnPseudoPrice->add($productSum);
        $productSumPrice = $productSumPseudoPrice->multiply(1 - $productDiscount / 100);
        $productSumNetPrice = $this->taxCalculator->getNetPriceFromDisplayPrice($productSumPrice);
        $productSumGrossPrice = $this->taxCalculator->getGrossPriceFromDisplayPrice($productSumPrice);

        // set product result
        $this->priceTable['discount'] = [
            'discount' => $productDiscount,
            'name' => $productDiscountName,
            'description' => $productDiscountDescription
        ];
        $this->priceTable['own'] = [
            'pseudoPrice' => $productOwnPseudoPrice,
            'price' => $productOwnPrice
        ];
        $this->priceTable['sum'] = [
            'pseudoPrice' => $productSumPseudoPrice,
            'price' => $productSumPrice,
            'netPrice' => $productSumNetPrice,
            'grossPrice' => $productSumGrossPrice
        ];
        $this->priceTable['productSurcharges'] = $productSurcharges;
    }

    /**
     * @param State $state
     * @param Currency $currency
     * @param TaxCalculator $taxCalculator
     * @param array $customerGroup
     * @param AptoUuid $productId
     * @param Currency|null $fallbackCurrency
     * @param float $currencyFactor
     * @return array
     * @throws AptoJsonSerializerException
     * @throws InvalidUuidException
     */
    protected function getCalculatedPrice(
        State $state,
        Currency $currency,
        TaxCalculator $taxCalculator,
        array $customerGroup,
        AptoUuid $productId,
        Currency $fallbackCurrency = null,
        float $currencyFactor = 1.0
    ): array {
        // set properties
        $this->state = $state;
        $this->currency = $currency;
        $this->taxCalculator = $taxCalculator;
        $this->customerGroup = $customerGroup;
        $this->productId = $productId;
        $this->fallbackCurrency = $fallbackCurrency !== null ? $fallbackCurrency : $currency;
        $this->currencyFactor = $currencyFactor;
        $this->elementIdIdentifierMapping = $this->getElementIdIdentifierMappingByProductId($productId);
        $this->computedValues = $this->computedProductValueCalculator->calculateComputedValues($productId->getId(), $state);

        // find and set fallback customer group
        $this->setFallbackCustomerGroup();

        // define types and key mapping
        $keyMapping = [
            'products' => 'productId',
            'sections' => 'sectionId',
            'elements' => 'elementId'
        ];
        $types = array_keys($keyMapping);

        // create customer group id
        $customerGroupId = new AptoUuid($customerGroup['id']);

        $shop = $this->shopFinder->findByDomain($this->requestStore->getHttpHost());

        // get prices/discounts/definitions by state
        $rawStatePrices = $this->productFinder->findPricesByState(
            $productId->getId(),
            $state,
            $currency->getCode(),
            $this->fallbackCurrency->getCode(),
            $customerGroupId->getId(),
            null === $this->fallbackCustomerGroupOrNull ? null : $this->fallbackCustomerGroupOrNull['id'],
            $shop['id']
        );

        if ($rawStatePrices['priceModifier'] === null || $rawStatePrices['priceModifier'] === '') {
            $rawStatePrices['priceModifier'] = 100;
        }

        $statePrices = [
            'prices' => $this->mapProperties($rawStatePrices['prices'], $keyMapping, true),
            'priceMatrices' => $this->mapProperties($rawStatePrices['priceMatrices'], $keyMapping),
            'priceFormulas' => $this->mapProperties($rawStatePrices['priceFormulas'], $keyMapping, true),
            'discounts' => $this->mapProperties($rawStatePrices['discounts'], $keyMapping),
            'definitions' => $this->mapPropertyToKey($rawStatePrices['definitions'], 'elementId'),
            'percentageSurcharges' => $this->mapProperties($rawStatePrices['percentageSurcharges'], $keyMapping)
        ];

        // init price table
        $this->priceTable = [
            'base' => [
                'surcharge' => 0.0,
                'price' => null
            ],
            'sections' => [],
            'discount' => 0.0,
            'own' => [
                'pseudoPrice' => null,
                'price' => null
            ],
            'sum' => [
                'pseudoPrice' => null,
                'price' => null,
                'netPrice' => null,
                'grossPrice' => null
            ]
        ];

        $this->priceModifier = floatval($rawStatePrices['priceModifier'] / 100 );

        $this->calculateBasePriceSurcharge($statePrices, $types);
        $this->calculateBasePrice($statePrices);
        $this->calculateElementPrices($statePrices);
        array_walk_recursive($this->priceTable, array($this, 'addDomainPriceModifier'));

        return $this->priceTable;
    }

    /**
     * @param AptoUuid $productId
     * @return array
     */
    protected function getElementIdIdentifierMappingByProductId(AptoUuid $productId): array
    {
        $elementIdIdentifierMapping = [];
        $product = $this->productFinder->findSectionsElements($productId->getId());
        if (null === $product) {
            return $elementIdIdentifierMapping;
        }

        foreach ($product['sections'] as $section) {
            foreach ($section['elements'] as $element) {
                $elementIdIdentifierMapping[$element['id']] = [
                    'sectionId' => $section['id'],
                    'elementId' => $element['id'],
                    'sectionIdentifier' => $section['identifier'],
                    'elementIdentifier' => $element['identifier']
                ];
            }
        }

        return $elementIdIdentifierMapping;
    }

    /**
     * set fallback customer group by using the appropriate finder method
     */
    protected function setFallbackCustomerGroup()
    {
        $this->fallbackCustomerGroupOrNull = $this->customerGroup['fallback'] ? null : $this->customerGroupFinder->findFallbackCustomerGroup();
    }

    /**
     * @param Money $price
     * @param Currency $currency
     * @param float $factor
     * @return Money
     */
    protected function convertCurrency(Money $price, Currency $currency, float $factor)
    {
        $pair = [
            $price->getCurrency()->getCode() => [
                $currency->getCode() => $factor
            ]
        ];

        $exchange = new FixedExchange($pair);
        $converter = new Converter($this->currencies, $exchange);

        return $converter->convert($price, $currency);
    }

    /**
     * @param $price
     * @return void
     */
    protected function addDomainPriceModifier(&$price)
    {
        if (is_a($price, Money::class)) {
            $price = $price->multiply($this->priceModifier);
        }
    }
}
