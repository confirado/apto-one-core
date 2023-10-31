<?php

namespace Apto\Catalog\Application\Core\Query\Configuration;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Application\Core\Service\AptoCache\AptoCacheService;
use Apto\Base\Domain\Core\Service\AptoJsonSerializer;
use Apto\Catalog\Application\Core\Query\Product\ProductFinder;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;

class FindHumanReadableStateHandler implements QueryHandlerInterface
{
    /**
     * @var ProductFinder
     */
    private $productFinder;

    /**
     * @var AptoJsonSerializer
     */
    private $aptoJsonSerializer;

    /**
     * FindHumanReadableStateHandler constructor.
     * @param ProductFinder $productFinder
     * @param AptoJsonSerializer $aptoJsonSerializer
     */
    public function __construct(
        ProductFinder $productFinder,
        AptoJsonSerializer $aptoJsonSerializer
    )
    {
        $this->productFinder = $productFinder;
        $this->aptoJsonSerializer = $aptoJsonSerializer;
    }

    /**
     * @param FindHumanReadableState $query
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function handle(FindHumanReadableState $query)
    {
        $productCacheKey = 'Configurable-Product-Raw-' . $query->getProductId();
        $product = AptoCacheService::getItem($productCacheKey);

        if (!$product) {
            $product = $this->productFinder->findConfigurableProductById($query->getProductId(), false);
            if (null === $product) {
                return [];
            }
            AptoCacheService::setItem($productCacheKey, $product);
        }

        // get data from query
        $state = new State($query->getState());

        // init readable state
        $readableState = [];

        foreach ($state->getElementList() as $sectionItem) {

            // skip non array values, like false/true for DefaultElement
            if (empty($sectionItem['values'])) {
                continue;
            }
            $humanReadableValues = $this->getHumanReadableValues($product, $sectionItem['elementId'], $sectionItem['values']);

            if (null !== $humanReadableValues) {
                // @todo what todo here it was saved under element id?
                $readableState[$sectionItem['elementId']] = $humanReadableValues;
            }
        }

        return $readableState;
    }

    /**
     * @param array $product
     * @param string $elementId
     * @param array $elementValues
     * @return array
     */
    protected function getHumanReadableValues(array $product, string $elementId, array $elementValues)
    {
        $elementDefinition = $this->getElementDefinitionByElementId($product, $elementId);
        if (null === $elementDefinition) {
            return [];
        }
        return $elementDefinition->getHumanReadableValues($elementValues);
    }

    /**
     * @param array $product
     * @param string $elementId
     * @return ElementDefinition|null
     */
    protected function getElementDefinitionByElementId(array $product, string $elementId)
    {
        foreach ($product['sections'] as $section) {
            foreach ($section['elements'] as $element) {
                if ($element['id'] == $elementId) {
                    /** @var ElementDefinition $elementDefinition */
                    $elementDefinition = $this->aptoJsonSerializer->jsonUnSerialize($element['definition']);
                    return $elementDefinition;
                }
            }
        }

        return null;
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindHumanReadableState::class => [
            'method' => 'handle',
            'bus' => 'query_bus'
        ];
    }
}
