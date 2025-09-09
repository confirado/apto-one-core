<?php

namespace Apto\Catalog\Application\Frontend\Service;

use Exception;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;

use Apto\Base\Application\Core\Service\RequestStore;
use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\CustomerGroup\CustomerGroupRepository;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Core\Model\Language\Language;
use Apto\Base\Domain\Core\Model\Language\LanguageRepository;

use Apto\Catalog\Application\Core\Service\PriceCalculator\StatePriceCalculator;
use Apto\Catalog\Application\Core\Service\ShopConnector\BasketConnector;
use Apto\Catalog\Application\Core\Service\ShopConnector\BasketItem;
use Apto\Catalog\Application\Core\Service\TaxCalculator\SimpleTaxCalculator;
use Apto\Catalog\Application\Core\Service\TaxCalculator\TaxCalculator;
use Apto\Catalog\Domain\Core\Model\Configuration\BasketConfiguration;
use Apto\Catalog\Domain\Core\Model\Configuration\Configuration;
use Apto\Catalog\Domain\Core\Model\Product\Product;
use Apto\Catalog\Domain\Core\Model\Shop\ShopRepository;
use Apto\Catalog\Domain\Core\Model\Product\ProductRepository;
use Apto\Catalog\Domain\Core\Model\Product\Repeatable;

class BasketItemFactory
{
    /**
     * @var RequestStore
     */
    protected $requestSessionStore;

    /**
     * @var ShopRepository
     */
    protected $shopRepository;

    /**
     * @var LanguageRepository
     */
    protected $languageRepository;

    /**
     * @var CustomerGroupRepository
     */
    protected $customerGroupRepository;

    /**
     * @var BasketConnector
     */
    protected $basketConnector;

    /**
     * @var StatePriceCalculator
     */
    protected $statePriceCalculator;

    /**
     * @var MediaFileSystemConnector
     */
    protected $mediaFileSystem;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var BasketItemDataRegistry
     */
    protected $basketItemDataRegistry;

    /**
     * @param RequestStore $requestSessionStore
     * @param ShopRepository $shopRepository
     * @param LanguageRepository $languageRepository
     * @param CustomerGroupRepository $customerGroupRepository
     * @param StatePriceCalculator $statePriceCalculator
     * @param BasketConnector $basketConnector
     * @param MediaFileSystemConnector $mediaFileSystem
     * @param ProductRepository $productRepository
     * @param BasketItemDataRegistry $basketItemDataRegistry
     */
    public function __construct(
        RequestStore $requestSessionStore,
        ShopRepository $shopRepository,
        LanguageRepository $languageRepository,
        CustomerGroupRepository $customerGroupRepository,
        StatePriceCalculator $statePriceCalculator,
        BasketConnector $basketConnector,
        MediaFileSystemConnector $mediaFileSystem,
        ProductRepository $productRepository,
        BasketItemDataRegistry $basketItemDataRegistry
    ) {
        $this->requestSessionStore = $requestSessionStore;
        $this->shopRepository = $shopRepository;
        $this->languageRepository = $languageRepository;
        $this->customerGroupRepository = $customerGroupRepository;
        $this->statePriceCalculator = $statePriceCalculator;
        $this->basketConnector = $basketConnector;
        $this->mediaFileSystem = $mediaFileSystem;
        $this->productRepository = $productRepository;
        $this->basketItemDataRegistry = $basketItemDataRegistry;
    }

    /**
     * @param BasketConfiguration $basketConfiguration
     * @param $connectorConfig
     * @param Product $product
     * @param AptoLocale $locale
     * @param bool $generateImages
     * @param array|string[] $perspectives
     * @param array $additionalData
     * @param bool $useProductPreviewImageAsFallback
     * @return BasketItem
     * @throws InvalidUuidException
     */
    public function makeBasketItem (
        BasketConfiguration $basketConfiguration,
        $connectorConfig,
        Product $product,
        AptoLocale $locale,
        bool $generateImages = true,
        array $perspectives = ['persp1'],
        array $additionalData = [],
        bool $useProductPreviewImageAsFallback = false
    ) {
        $additionalDataTranslations = [];
        if (isset($additionalData['translations'])) {
            $additionalDataTranslations = $additionalData['translations'];
        }

        $additionalData = array_merge($additionalData, [
            'productId' => $product->getId()->getId(),
            'productUrl' => $product->getSeoUrl(),
            'articleNumber' => $product->getArticleNumber(),
            'metaTitle' => $product->getMetaTitle()->getTranslation($locale, null, true)->getValue(),
            'metaDescription' => $product->getMetaDescription()->getTranslation($locale, null, true)->getValue(),
            'description' => $product->getDescription()->getTranslation($locale, null, true)->getValue(),
            'stock' => $product->getStock(),
            'maxPurchase' => $product->getMaxPurchase(),
            'weight' => $product->getWeight(),
            'width' => 0.0,
            'length' => 0.0,
            'height' => 0.0,
            'deliveryTime' => $product->getDeliveryTime(),
            'locale' => $locale->getName(),
            'position' => $product->getPosition(),
            'customProperties' => [
                'productProperties' => $this->getCustomPropertiesOfProduct($basketConfiguration),
                'elementProperties' => $this->getCustomPropertiesOfSelectedElements($basketConfiguration),
            ],
            'translations' => [
                'root' => [
                    'title' => $product->getName()->jsonSerialize()
                ],
                'additionalData' => [
                    'description' => $product->getDescription()->jsonSerialize(),
                    'metaTitle' => $product->getMetaTitle()->jsonSerialize(),
                    'metaDescription' => $product->getMetaDescription()->jsonSerialize()
                ]
            ]
        ]);

        // create basket item
        if ($connectorConfig === null) {
            $shopId = $this->shopRepository->findConnectorConfigByDomain($this->requestSessionStore->getHttpHost())['shopId'];
            $currency = new Currency($this->shopRepository->findConnectorConfigByDomain($this->requestSessionStore->getHttpHost())['currency']);
            $fallbackCurrency = $currency;
            $currencyFactor = 1.0;
        } else {
            $shopId =  $connectorConfig->getShopId();
            $connectorState = $this->basketConnector->getState($connectorConfig);
            $displayCurrency = $connectorState['result']['displayCurrency'];
            $currency = new Currency($displayCurrency['currency']);
            $fallbackCurrency = new Currency($connectorConfig->getCurrency()); // @todo set fallback currency
            $currencyFactor = $displayCurrency['factor'];
        }

        if (isset($additionalDataTranslations['root']['properties'])) {
            $propertyTranslations = $additionalDataTranslations['root']['properties'];
        } else {
            $propertyTranslations = $this->getTranslatedProperties($basketConfiguration);
        }

        if (isset($additionalDataTranslations['root']['sortedProperties'])) {
            $sortedPropertyTranslations = $additionalDataTranslations['root']['sortedProperties'];
        } else {
            $sortedPropertyTranslations = $this->getTranslatedSortedProperties($basketConfiguration);
        }

        $additionalData['translations']['root'] = array_merge($additionalData['translations']['root'], [
            'properties' => $propertyTranslations
        ]);

        $additionalData['translations']['root'] = array_merge($additionalData['translations']['root'], [
            'sortedProperties' => $sortedPropertyTranslations
        ]);

        // get Images
        $images = [];
        if ($generateImages) {
            if (array_key_exists('productImages', $additionalData)) {
                $images = $additionalData['productImages'];
            }

            // use product preview image as fallback
            if (count($images) < 1 && $useProductPreviewImageAsFallback && $product->getPreviewImage() !== null) {
                $images[] = $this->requestSessionStore->getSchemeAndHttpHost() . $this->mediaFileSystem->getFileUrl($product->getPreviewImage()->getFile());
            }
        }

        $customerGroupPrices = $this->getCustomerGroupPrices(
            $shopId,
            $basketConfiguration,
            $currency,
            $fallbackCurrency,
            $currencyFactor
        );

        $additionalData['prices'] = $customerGroupPrices[1];

        // BasketItem Data Provider
        foreach ($this->basketItemDataRegistry->getBasketItemDataProviders() as $basketItemDataProvider) {
            /** @var BasketItemDataProvider $basketItemDataProvider */
            $additionalData = $basketItemDataProvider->getData($additionalData, new AptoUuid($shopId), $basketConfiguration, $locale, $currency, $fallbackCurrency);
        }

        return new BasketItem(
            $basketConfiguration->getId()->getId(),
            $product->getName()->getTranslation($locale, null, true)->getValue(),
            $product->getTaxRate(),
            $customerGroupPrices[0],
            [],
            $this->getProperties($basketConfiguration, $locale),
            $images,
            $additionalData
        );
    }

    /**
     * @param Configuration $configuration
     * @param AptoLocale $locale
     * @return array
     * @throws InvalidUuidException
     */
    protected function getProperties(Configuration $configuration, AptoLocale $locale): array
    {
        $properties = [];

        foreach ($configuration->getState()->getStateWithoutParameters() as $sectionItem) {
            $sectionId = $sectionItem['sectionId'];
            $elementId = $sectionItem['elementId'];
            $values = $sectionItem['values'];

            $sectionUuId = new AptoUuid($sectionId);
            $sectionName = $configuration->getProduct()->getSectionName($sectionUuId);

            $sectionTranslatedName = $sectionName->getTranslation($locale, null, true);
            $sectionTranslatedNameValue = $sectionTranslatedName->getValue();

            $elementUuId = new AptoUuid($elementId);
            $elementName = $configuration->getProduct()->getElementName($sectionUuId, $elementUuId);
            $elementTranslatedName = $elementName->getTranslation($locale, null, true);
            $elementDefinition = $configuration->getProduct()->getElementDefinition(
                new AptoUuid($sectionId),
                new AptoUuid($elementId)
            );
            $elementTranslatedNameValue = $elementTranslatedName->getValue();

            if (!empty($values)) {
                $humanReadableValues = $elementDefinition->getHumanReadableValues($values);

                // @todo we need a better approach here, every value needs a description like height => 5 -> Höhe: 5m/cm etc.
                // @todo we also cant save customer input in shop properties which are used for filtering
                if (count($humanReadableValues) > 0) {

                    // @todo find a better way to let element definition decide how to generate property values
                    if (method_exists($elementDefinition, 'overrideShopPropertyValues')) {
                        $properties = $elementDefinition->overrideShopPropertyValues($properties, $sectionName, $elementName, $humanReadableValues, $locale);
                    } else {
                        foreach ($humanReadableValues as $key => $humanReadableValue) {

                            // @todo find a better way to let element definition decide how to generate property value
                            if (method_exists($elementDefinition, 'overrideShopPropertyValue')) {
                                $properties[$sectionTranslatedNameValue][] = $elementDefinition->overrideShopPropertyValue($key, $sectionName, $elementName, $humanReadableValue, $locale);
                            } else {

                                /** @var AptoTranslatedValue $humanReadableValue */
                                $properties[$sectionTranslatedNameValue][] = $elementTranslatedNameValue . ' - ' . $humanReadableValue->getTranslation($locale, null, true)->getValue();
                            }
                        }
                    }
                } else {
                    // bugfix: element should be displayed, even if it's empty
                    $properties[$sectionTranslatedNameValue][] = $elementTranslatedNameValue;
                }
            } else {
                $properties[$sectionTranslatedNameValue][] = $elementTranslatedNameValue;
            }
        }

        // remove duplicate values to avoid confusion in AptoShopConnectors
        foreach ($properties as $key => $property) {
            $properties[$key] = array_unique($properties[$key]);
        }

        return $properties;
    }

    /**
     * @param Configuration $configuration
     * @param AptoLocale $locale
     * @return array
     * @throws InvalidUuidException
     */
    protected function getSortedProperties(Configuration $configuration, AptoLocale $locale): array
    {
        $sortedProperties = [];
        $product = $configuration->getProduct();
        $state = $configuration->getState();
        $sectionCount = 0;

        foreach ($state->getSectionList() as $section) {
            $sectionId = new AptoUuid($section['sectionId']);
            $repetition = $section['repetition'];
            if (!$state->isSectionActive($sectionId, $repetition)) {
                continue;
            }

            $sectionName = $product->getSectionName($sectionId)->getTranslation($locale, null, true)->getValue();
            $repeatable = $product->getSectionRepeatable($sectionId);
            if ($repeatable && $repeatable->getType() === Repeatable::TYPES[1]) {
                $sectionName .= ' ' . ($section['repetition'] + 1);
            }

            $sortedProperties[$sectionCount] = [
                'sectionId' => $sectionId->getId(),
                'name' => $sectionName,
                'position' => $product->getSectionPosition($sectionId),
                'repetition' => $section['repetition'],
                'elements' => []
            ];

            /** @var AptoUuid $elementId */
            foreach ($product->getElementIds($sectionId) as $elementId) {
                if (!$state->isElementActive($sectionId, $elementId, $repetition)) {
                    continue;
                }

                $elementState = $state->getElementState($sectionId, $elementId, $repetition);
                $elementDefinition = $product->getElementDefinition($sectionId, $elementId);
                $elementName = $product->getElementName($sectionId, $elementId)->getTranslation($locale, null, true)->getValue();
                $elementPosition = $product->getElementPosition($sectionId, $elementId);

                $sortedProperties[$sectionCount]['elements'][] = [
                    'elementId' => $elementId->getId(),
                    'name' => $elementName,
                    'position' => $elementPosition,
                    'properties' => []
                ];

                if (!empty($elementState)) {
                    $humanReadableValues = $elementDefinition->getHumanReadableValues($elementState);

                    /** @var AptoTranslatedValue $humanReadableValue */
                    foreach ($humanReadableValues as $key => $humanReadableValue) {
                        $currentElementIndex = count($sortedProperties[$sectionCount]['elements']) - 1;
                        $humanReadableValueName = $humanReadableValue->getTranslation($locale, null, true)->getValue();

                        if (!trim($humanReadableValueName)) {
                            continue;
                        }

                        $sortedProperties[$sectionCount]['elements'][$currentElementIndex]['properties'][] = [
                            'name' => $humanReadableValueName
                        ];
                    }
                }
            }

            // sort elements in section
            usort($sortedProperties[$sectionCount]['elements'], function($first, $second) {
                if ($first['position'] > $second['position']) {
                    return 1;
                }

                if ($first['position'] < $second['position']) {
                    return -1;
                }

                return 0;
            });
            $sectionCount++;
        }

        // sort sections
        usort($sortedProperties, function($first, $second) {
            if ($first['position'] > $second['position']) {
                return 1;
            }

            if ($first['position'] < $second['position']) {
                return -1;
            }

            return 0;
        });

        return $sortedProperties;
    }

    /**
     * @param Configuration $configuration
     * @return array
     * @throws InvalidUuidException
     */
    protected function getTranslatedProperties(Configuration $configuration): array
    {
        $languages = $this->languageRepository->findAll();
        $translatedProperties = [];
        /** @var Language $language */
        foreach ($languages as $language)
        {
            $translatedProperties[$language->getIsocode()->getName()] = $this->getProperties($configuration,$language->getIsocode());
        }
        return $translatedProperties;
    }

    /**
     * @param Configuration $configuration
     * @return array
     * @throws InvalidUuidException
     */
    protected function getTranslatedSortedProperties(Configuration $configuration): array
    {
        $languages = $this->languageRepository->findAll();
        $translatedProperties = [];
        /** @var Language $language */
        foreach ($languages as $language)
        {
            $translatedProperties[$language->getIsocode()->getName()] = $this->getSortedProperties($configuration, $language->getIsocode());
        }

        return $translatedProperties;
    }

    /**
     * @param string $shopId
     * @param BasketConfiguration $basketConfiguration
     * @param Currency $currency
     * @param Currency|null $fallbackCurrency
     * @param float $currencyFactor
     * @return array
     * @throws Exception
     */
    protected function getCustomerGroupPrices(
        string $shopId,
        BasketConfiguration $basketConfiguration,
        Currency $currency,
        Currency $fallbackCurrency = null,
        float $currencyFactor = 1.0
    ): array {
        $formattedPrices = [];
        $customerGroupPrices = [];

        $customerGroups = $this->customerGroupRepository->findAllExternalAndUuidsByShopId($shopId);

        if (null === $customerGroups || count($customerGroups) < 1) {
            throw new \UnexpectedValueException('Can not add Configuration to Basket because no external CustomerGroups are found.');
        }

        foreach ($customerGroups as $customerGroup) {
            $groupExternalId = $customerGroup['externalId'];

            $taxCalculator = new SimpleTaxCalculator(
                (string) $basketConfiguration->getProduct()->getTaxRate(),
                $customerGroup['inputGross'],
                $customerGroup['showGross']
            );

            /**
             * @deprecated
             * get price from temp configuration
             */
            $configurationPrice = $this->getConfigurationPrice(
                $basketConfiguration,
                $currency,
                $customerGroup,
                $taxCalculator,
                $fallbackCurrency,
                $currencyFactor
            );

            /**
             * @deprecated
             * format configuration price
             */
            $formattedPrices[$groupExternalId] = $this->getFormattedAmount($configurationPrice);

            // get prices from temp configuration
            $configurationPrices = $this->getConfigurationPrices(
                $basketConfiguration,
                $currency,
                $customerGroup,
                $taxCalculator,
                $fallbackCurrency,
                $currencyFactor
            );

            // format configuration prices
            $customerGroupPrices[$groupExternalId] = [
                'pseudoPrice' => $this->getFormattedAmount($configurationPrices['pseudoPrice']),
                'price' => $this->getFormattedAmount($configurationPrices['price']),
                'netPrice' => $this->getFormattedAmount($configurationPrices['netPrice']),
                'grossPrice' => $this->getFormattedAmount($configurationPrices['grossPrice'])
            ];
        }

        return [
            $formattedPrices,
            $customerGroupPrices
        ];
    }

    /**
     * @deprecated
     * @param Configuration $configuration
     * @param Currency $currency
     * @param array $customerGroup
     * @param TaxCalculator $taxCalculator
     * @param Currency|null $fallbackCurrency
     * @param float $currencyFactor
     * @return Money
     */
    protected function getConfigurationPrice(
        Configuration $configuration,
        Currency $currency,
        array $customerGroup,
        TaxCalculator $taxCalculator,
        Currency $fallbackCurrency = null,
        float $currencyFactor = 1.0
    ): Money {
        // @todo because statePriceCalculator uses a finder here we break the rule "Dont use Finders in commands"
        $prices = $this->statePriceCalculator->getRawPrices(
            $configuration->getProduct()->getId(),
            $customerGroup,
            $currency,
            $configuration->getState(),
            $taxCalculator,
            $fallbackCurrency,
            $currencyFactor
        );
        return $prices['sum']['price'];

        /*$configurationPrices = $configuration->getConfigurationPrices(
            $currency,
            $customerGroupId
        );

        $this->statePriceCalculator->setProductPrices($configurationPrices['productPrices']);
        $this->statePriceCalculator->setSectionPrices($configurationPrices['sectionPrices']);
        $this->statePriceCalculator->setElementPrices($configurationPrices['elementPrices']);

        return $this->statePriceCalculator->getCalculatedPrice($currency);*/
    }

    /**
     * @param Configuration $configuration
     * @param Currency $currency
     * @param array $customerGroup
     * @param TaxCalculator $taxCalculator
     * @param Currency|null $fallbackCurrency
     * @param float $currencyFactor
     * @return array
     */
    protected function getConfigurationPrices(
        Configuration $configuration,
        Currency $currency,
        array $customerGroup,
        TaxCalculator $taxCalculator,
        Currency $fallbackCurrency = null,
        float $currencyFactor = 1.0
    ): array {
        // @todo because statePriceCalculator uses a finder here we break the rule "Dont use Finders in commands"
        $prices = $this->statePriceCalculator->getDisplayPrices(
            $configuration->getProduct()->getId(),
            $customerGroup,
            $currency,
            $configuration->getState(),
            $taxCalculator,
            $fallbackCurrency,
            $currencyFactor
        );
        return $prices['sum'];
    }

    /**
     * @param array $renderImages
     * @param string $productId
     * @param string $perspective
     * @return array
     */
    protected function getFlattenRenderImages(array $renderImages, string $productId, string $perspective): array
    {
        $flattenImages = [];
        foreach ($renderImages as $renderImage) {
            /**
             * @var AptoUuid $renderImageId
             * @var File $mediaFile
             */
            $renderImageId = $renderImage['id'];
            $mediaFile = $renderImage['mediaFile'];
            $flattenImages[] = [
                'productId' => $productId,
                'renderImageId' => $renderImageId->getId(),
                'layer' => $renderImage['layer'],
                'perspective' => $perspective,
                'path' => $mediaFile->getDirectory()->getPath(),
                'filename' => $mediaFile->getFilename(),
                'extension' => $mediaFile->getExtension(),
                'offsetX' => $renderImage['offsetX'],
                'offsetY' => $renderImage['offsetY']
            ];
        }

        return $flattenImages;
    }

    /**
     * @param Money $configurationPrice
     * @return bool|string
     * @throws Exception
     */
    protected function getFormattedAmount(Money $configurationPrice)
    {
        // @ todo maybe we need a formatter service here
        // format configuration price
        $currencies = new ISOCurrencies();
        $moneyFormatter = new DecimalMoneyFormatter($currencies);
        $formattedAmount = $moneyFormatter->format($configurationPrice);

        return $formattedAmount;
    }

    /**
     * @param BasketConfiguration $basketConfiguration
     * @return array
     */
    protected function getCustomPropertiesOfSelectedElements(BasketConfiguration $basketConfiguration): array
    {
        $customPropertiesArray = [];
        $elementIds = $basketConfiguration->getState()->getElementIds();
        $elements = $this->productRepository->findElementCustomPropertiesAsArray($elementIds);
        foreach ($elements['data'] as $element) {
            foreach ($element['customProperties'] as $customProperty) {
                if ($customProperty['translatable']) {
                    continue;
                }
                if (!array_key_exists($element['identifier'], $customPropertiesArray)) {
                    $customPropertiesArray[$element['identifier']] = [];
                }
                $customPropertiesArray[$element['identifier']][$customProperty['key']] = $customProperty['value'];
            }
        }
        return $customPropertiesArray;
    }

    protected function getCustomPropertiesOfProduct(BasketConfiguration $basketConfiguration): array
    {
        $customPropertiesArray = [];
        $productID = $basketConfiguration->getProduct()->getId()->getId();
        //We can reference ['data'][0] here since we only query one productID
        $productCustomProperties = $this->productRepository->findProductCustomPropertiesAsArray([$productID])['data'][0];
        foreach ($productCustomProperties['customProperties'] as $customProperty) {
            if ($customProperty['translatable']) {
                continue;
            }
            $customPropertiesArray[$customProperty['key']] = $customProperty['value'];
        }
        return $customPropertiesArray;
    }
}
