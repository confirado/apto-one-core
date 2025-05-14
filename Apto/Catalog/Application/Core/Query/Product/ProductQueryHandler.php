<?php

namespace Apto\Catalog\Application\Core\Query\Product;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Application\Core\Service\AptoCache\AptoCacheService;
use Apto\Base\Application\Core\Service\RequestStore;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Service\AptoJsonSerializer;
use Apto\Catalog\Application\Core\Query\Shop\ShopFinder;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\ComputedProductValueCalculator;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementValueCollection;

use Apto\Base\Domain\Core\Service\AptoJsonSerializerException;

class ProductQueryHandler implements QueryHandlerInterface
{
    /**
     * @var ProductFinder
     */
    protected $productFinder;

    /**
     * @var AptoJsonSerializer
     */
    protected $aptoJsonSerializer;

    /**
     * @var MediaFileSystemConnector
     */
    protected $fileSystemConnector;

    /**
     * @var ComputedProductValueCalculator
     */
    protected $computedProductValueCalculator;

    /**
     * @var ShopFinder
     */
    private $shopFinder;

    /**
     * @var RequestStore
     */
    private $requestStore;

    /**
     * @param ProductFinder $productFinder
     * @param AptoJsonSerializer $aptoJsonSerializer
     * @param MediaFileSystemConnector $fileSystemConnector
     * @param ComputedProductValueCalculator $computedProductValueCalculator
     * @param ShopFinder $shopFinder
     * @param RequestStore $requestStore
     */
    public function __construct(
        ProductFinder $productFinder,
        AptoJsonSerializer $aptoJsonSerializer,
        MediaFileSystemConnector $fileSystemConnector,
        ComputedProductValueCalculator $computedProductValueCalculator,
        ShopFinder $shopFinder,
        RequestStore $requestStore
    ) {
        $this->productFinder = $productFinder;
        $this->aptoJsonSerializer = $aptoJsonSerializer;
        $this->fileSystemConnector = $fileSystemConnector;
        $this->computedProductValueCalculator = $computedProductValueCalculator;
        $this->shopFinder = $shopFinder;
        $this->requestStore = $requestStore;
    }

    /**
     * @param FindProduct $query
     * @return array|null
     */
    public function handleFindProduct(FindProduct $query)
    {
        $productId = $query->getId();
        $cacheId = 'AptoProduct-' . $productId;
        $product = AptoCacheService::getItem($cacheId);
        if ($product) {
            return $product;
        }
        $product = $this->productFinder->findById($productId);
        AptoCacheService::setItem($cacheId, $product);

        return $this->productFinder->findById($query->getId());
    }

    /**
     * @param FindProducts $query
     * @return array
     */
    public function handleFindProducts(FindProducts $query)
    {
        return $this->productFinder->findByListingPageNumber($query->getPageNumber(), $query->getRecordsPerPage(), $query->getSearchString());
    }

    /**
     * @param FindProductsByCategoryIdentifier $query
     * @return array
     */
    public function handleFindProductsByCategoryIdentifier(FindProductsByCategoryIdentifier $query)
    {
        return $this->productFinder->findByCategoryIdentifier($query->getCategoryIdentifier());
    }

    /**
     * @param FindProductsByFilter $query
     * @return array
     */
    public function handleFindProductsByFilter(FindProductsByFilter $query)
    {
        $host = $this->requestStore->getHttpHost();
        $shop = $this->shopFinder->findByDomain($host);
        $filterHash = md5(serialize($query->getFilter()) . '-' . $host);

        $list = AptoCacheService::getItem('ProductList-' . $filterHash);
        if ($list) {
            return $list;
        }

        $list = $this->productFinder->findByFilter($query->getFilter());
        foreach ($list['data'] as $key => &$product) {
            if (!$this->hasProductShop($product, $shop)) {
                unset($list['data'][$key]);
                $list['numberOfRecords']--;
                continue;
            }

            $domainProperties = $this->getCurrentDomainProperties($product, $shop);

            if (isset($domainProperties) && $domainProperties['previewImage'] && $domainProperties['previewImageMediaFile']) {
                $product['previewImage'] = $domainProperties['previewImage'];
                $product['previewImageMediaFile'] = $domainProperties['previewImageMediaFile'];
            }
        }

        $list['data'] = array_values($list['data']);
        AptoCacheService::setItem('ProductList-' . $filterHash, $list);
        return $list;
    }

    /**
     * @param FindProductsByFilterPagination $query
     * @return array
     */
    public function handleFindProductsByFilterPagination(FindProductsByFilterPagination $query)
    {
        return $this->productFinder->findByFilterPagination($query->getPageNumber(), $query->getRecordsPerPage(), $query->getFilter(), false);
    }

    /**
     * @param FindProductIdsByFilter $query
     * @return array
     */
    public function handleFindProductIdsByFilter(FindProductIdsByFilter $query)
    {
        return $this->productFinder->findProductIdsByFilter($query->getFilter(), false);
    }

    /**
     * @param FindProductSections $query
     * @return array|null
     */
    public function handleFindProductSections(FindProductSections $query)
    {
        $productId = $query->getId();
        $cacheId = 'AptoProduct-Sections-' . $productId;
        $sections = AptoCacheService::getItem($cacheId);
        if ($sections) {
            return $sections;
        }
        $sections = $this->productFinder->findSections($productId);
        AptoCacheService::setItem($cacheId, $sections);

        return $sections;
    }

    /**
     * @param FindProductSectionsElements $query
     * @return array|null
     * @throws AptoJsonSerializerException
     */
    public function handleFindProductSectionsElements(FindProductSectionsElements $query)
    {
        $productId = $query->getId();
        $cacheId = 'AptoProduct-Sections-Elements-' . $productId;
        $sectionsElements = AptoCacheService::getItem($cacheId);
        if ($sectionsElements) {
            return $sectionsElements;
        }
        $sectionsElements = $this->productFinder->findSectionsElements($query->getId());
        if (isset($sectionsElements['sections'])) {
            foreach ($sectionsElements['sections'] as $iSection => &$section) {
                if (isset($section['elements'])) {
                    foreach ($section['elements'] as $iElement => &$element) {
                        // set element definition
                        /** @var ElementDefinition $elementDefinition */
                        $elementDefinition = $this->aptoJsonSerializer->jsonUnSerialize($element['definition']);

                        $definition = [];
                        $definition['name'] = $elementDefinition::getName();
                        $definition['component'] = $elementDefinition::getFrontendComponent();

                        // set selectable values
                        $selectableValues = $elementDefinition->getSelectableValues();
                        $pseudoSelectedValues = [];
                        /** @var ElementValueCollection $selectableValue*/
                        foreach ($selectableValues as $selectableProperty => $selectableValue) {
                            $definition['properties'][$selectableProperty] = $selectableValue->jsonSerialize();
                            $pseudoSelectedValues[$selectableProperty] = $selectableValue->getAnyValue();
                        }

                        // set computable values
                        $definition['computableValues'] = array_keys($elementDefinition->getComputableValues($pseudoSelectedValues));

                        // set element definition
                        $element['definition'] = $definition;
                    }
                }
            }
        }
        AptoCacheService::setItem($cacheId, $sectionsElements);

        return $sectionsElements;
    }

    /**
     * @param FindProductRules $query
     * @return array|null
     */
    public function handleFindProductRules(FindProductRules $query)
    {
        return $this->productFinder->findRules($query->getId());
    }

    /**
     * @param FindProductComputedValues $query
     * @return array|null
     */
    public function handleFindProductComputedValues(FindProductComputedValues $query)
    {
        return $this->productFinder->findComputedValues($query->getId());
    }

    /**
     * @param FindProductPrices $query
     * @return array|null
     */
    public function handleFindProductPrices(FindProductPrices $query)
    {
        return $this->productFinder->findPrices($query->getId());
    }

    /**
     * @param FindProductDiscounts $query
     * @return array|null
     */
    public function handleFindProductDiscounts(FindProductDiscounts $query)
    {
        return $this->productFinder->findDiscounts($query->getId());
    }

    /**
     * @param FindProductByIdentifier $query
     * @return array|null
     */
    public function handleFindProductByIdentifier(FindProductByIdentifier $query)
    {
        return$this->productFinder->findProductByIdentifier($query->getIdentifier());
    }

    /**
     * @param FindProductIdByIdentifier $query
     * @return array|null
     */
    public function handleFindProductIdByIdentifier(FindProductIdByIdentifier $query)
    {
        return$this->productFinder->findProductIdByIdentifier($query->getProductIdentifier());
    }

    /**
     * @param FindSectionIdByIdentifier $query
     * @return array|null
     */
    public function handleFindSectionIdByIdentifier(FindSectionIdByIdentifier $query)
    {
        return$this->productFinder->findSectionIdByIdentifier($query->getProductIdentifier(), $query->getSectionIdentifier());
    }

    /**
     * @param FindElementIdByIdentifier $query
     * @return array|null
     */
    public function handleFindElementIdByIdentifier(FindElementIdByIdentifier $query)
    {
        return$this->productFinder->findElementIdByIdentifier($query->getProductIdentifier(), $query->getSectionIdentifier(), $query->getElementIdentifier());
    }

    /**
     * @param FindProductCustomProperties $query
     * @return array|null
     */
    public function handleFindProductCustomProperties(FindProductCustomProperties $query)
    {
        return $this->productFinder->findCustomProperties($query->getId());
    }

    /**
     * @param FindNextAvailablePosition $query
     * @return int
     */
    public function handleFindNextAvailablePosition(FindNextAvailablePosition $query)
    {
        return $this->productFinder->findNextPosition();
    }

    /**
     * Get product's calculated values (Berechnete werte)
     *
     * @param FindProductComputedValuesCalculated $query
     * @return array
     * @throws \Apto\Base\Domain\Core\Model\InvalidUuidException
     * @throws \Apto\Catalog\Application\Core\Service\ComputedProductValue\CircularReferenceException
     */
    public function handleFindProductComputedValuesCalculated(FindProductComputedValuesCalculated $query)
    {
        $state = new State($query->getState());

        return $this->computedProductValueCalculator->calculateComputedValues($query->getId(), $state);
    }

    /**
     * @param array|null $product
     * @param array|null $shop
     * @return bool
     */
    private function hasProductShop(?array $product, ?array $shop ): bool
    {
        if (!$product || !$shop){
            return false;
        }

        foreach ($product['shops'] as $pShop) {
            if ($pShop['id'] === $shop['id']) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindProduct::class => [
            'method' => 'handleFindProduct',
            'bus' => 'query_bus'
        ];

        yield FindProducts::class => [
            'method' => 'handleFindProducts',
            'bus' => 'query_bus'
        ];

        yield FindProductsByCategoryIdentifier::class => [
            'method' => 'handleFindProductsByCategoryIdentifier',
            'bus' => 'query_bus'
        ];

        yield FindProductsByFilterPagination::class => [
            'method' => 'handleFindProductsByFilterPagination',
            'bus' => 'query_bus'
        ];

        yield FindProductIdsByFilter::class => [
            'method' => 'handleFindProductIdsByFilter',
            'bus' => 'query_bus'
        ];

        yield FindProductsByFilter::class => [
            'method' => 'handleFindProductsByFilter',
            'bus' => 'query_bus'
        ];

        yield FindProductSections::class => [
            'method' => 'handleFindProductSections',
            'bus' => 'query_bus'
        ];

        yield FindProductSectionsElements::class => [
            'method' => 'handleFindProductSectionsElements',
            'bus' => 'query_bus'
        ];

        yield FindProductRules::class => [
            'method' => 'handleFindProductRules',
            'bus' => 'query_bus'
        ];

        yield FindProductComputedValues::class => [
            'method' => 'handleFindProductComputedValues',
            'bus' => 'query_bus'
        ];

        yield FindProductPrices::class => [
            'method' => 'handleFindProductPrices',
            'bus' => 'query_bus'
        ];

        yield FindProductDiscounts::class => [
            'method' => 'handleFindProductDiscounts',
            'bus' => 'query_bus'
        ];

        yield FindProductByIdentifier::class => [
            'method' => 'handleFindProductByIdentifier',
            'bus' => 'query_bus'
        ];

        yield FindProductIdByIdentifier::class => [
            'method' => 'handleFindProductIdByIdentifier',
            'bus' => 'query_bus'
        ];

        yield FindSectionIdByIdentifier::class => [
            'method' => 'handleFindSectionIdByIdentifier',
            'bus' => 'query_bus'
        ];

        yield FindElementIdByIdentifier::class => [
            'method' => 'handleFindElementIdByIdentifier',
            'bus' => 'query_bus'
        ];

        yield FindProductCustomProperties::class => [
            'method' => 'handleFindProductCustomProperties',
            'bus' => 'query_bus'
        ];

        yield FindNextAvailablePosition::class => [
            'method' => 'handleFindNextAvailablePosition',
            'bus' => 'query_bus'
        ];

        yield FindProductComputedValuesCalculated::class => [
            'method' => 'handleFindProductComputedValuesCalculated',
            'bus' => 'query_bus'
        ];
    }

    /**
     * @param array $product
     * @param array $shop
     * @return array|null
     */
    private function getCurrentDomainProperties(array $product, array $shop): ?array
    {
        if (!empty($product['domainProperties'])) {
            foreach ($product['domainProperties'] as $domainProperties) {
                if ($domainProperties['shop']['id'] === $shop['id']) {
                    return $domainProperties;
                }
            }
        }
        return null;
    }
}
