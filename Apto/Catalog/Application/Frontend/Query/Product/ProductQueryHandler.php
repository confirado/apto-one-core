<?php

namespace Apto\Catalog\Application\Frontend\Query\Product;

use Apto\Catalog\Application\Core\Query\Configuration\BasketConfigurationFinder;
use Apto\Catalog\Application\Core\Query\Configuration\CodeConfigurationFinder;
use Apto\Catalog\Application\Core\Query\Configuration\GuestConfigurationFinder;
use Apto\Catalog\Application\Core\Query\Configuration\OrderConfigurationFinder;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Exception\CacheException;

use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Domain\Core\Service\AptoJsonSerializer;
use Apto\Base\Domain\Core\Service\AptoJsonSerializerException;
use Apto\Base\Domain\Core\Service\AptoParameterInterface;
use Apto\Catalog\Application\Core\Query\Configuration\ImmutableConfigurationFinder;
use Apto\Catalog\Application\Core\Service\ConfigurableProduct\ConfigurableProductBuilder;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;

class ProductQueryHandler implements QueryHandlerInterface
{
    /**
     * @var ConfigurableProductBuilder
     */
    private ConfigurableProductBuilder $configurableProductBuilder;

    /**
     * @var GuestConfigurationFinder
     */
    private GuestConfigurationFinder $guestConfigurationFinder;

    /**
     * @var BasketConfigurationFinder
     */
    private BasketConfigurationFinder $basketConfigurationFinder;

    /**
     * @var OrderConfigurationFinder
     */
    private OrderConfigurationFinder $orderConfigurationFinder;

    /**
     * @var CodeConfigurationFinder
     */
    private CodeConfigurationFinder $codeConfigurationFinder;

    /**
     * @var ImmutableConfigurationFinder
     */
    private ImmutableConfigurationFinder $immutableConfigurationFinder;

    /**
     * @var AptoJsonSerializer
     */
    protected AptoJsonSerializer $aptoJsonSerializer;

    /**
     * @var string
     */
    protected string $defaultProduct;

    /**
     * @param ConfigurableProductBuilder $configurableProductBuilder
     * @param GuestConfigurationFinder $guestConfigurationFinder
     * @param BasketConfigurationFinder $basketConfigurationFinder
     * @param OrderConfigurationFinder $orderConfigurationFinder
     * @param CodeConfigurationFinder $codeConfigurationFinder
     * @param ImmutableConfigurationFinder $immutableConfigurationFinder
     * @param AptoJsonSerializer $aptoJsonSerializer
     * @param AptoParameterInterface $aptoParameter
     */
    public function __construct(
        ConfigurableProductBuilder $configurableProductBuilder,
        GuestConfigurationFinder $guestConfigurationFinder,
        BasketConfigurationFinder $basketConfigurationFinder,
        OrderConfigurationFinder $orderConfigurationFinder,
        CodeConfigurationFinder $codeConfigurationFinder,
        ImmutableConfigurationFinder $immutableConfigurationFinder,
        AptoJsonSerializer $aptoJsonSerializer,
        AptoParameterInterface $aptoParameter
    ) {
        $this->configurableProductBuilder = $configurableProductBuilder;
        $this->guestConfigurationFinder = $guestConfigurationFinder;
        $this->basketConfigurationFinder = $basketConfigurationFinder;
        $this->orderConfigurationFinder = $orderConfigurationFinder;
        $this->codeConfigurationFinder = $codeConfigurationFinder;
        $this->immutableConfigurationFinder = $immutableConfigurationFinder;
        $this->aptoJsonSerializer = $aptoJsonSerializer;
        $this->defaultProduct = $aptoParameter->get('default_product');
    }

    /**
     * @param FindConfigurableProduct $query
     * @return mixed|null
     * @throws AptoJsonSerializerException
     * @throws InvalidArgumentException
     * @throws CacheException
     */
    public function handleFindConfigurableProduct(FindConfigurableProduct $query)
    {
        $productId = $query->getProductId() ? $query->getProductId() : $this->defaultProduct;
        return $this->configurableProductBuilder->createConfigurableProduct($productId, false, false);
    }

    /**
     * @param FindProductConfiguration $query
     * @return array|null
     * @throws AptoJsonSerializerException
     * @throws CacheException
     * @throws InvalidArgumentException
     */
    public function handleFindProductConfiguration(FindProductConfiguration $query): ?array
    {
        // get configurable product if no configuration type is set
        if (null === $query->getType()) {
            $productId = $query->getId() ?: $this->defaultProduct;
            $product = $this->configurableProductBuilder->createConfigurableProduct($productId, false, false);

            if (null === $product) {
                return null;
            }

            return [
                'product' => $this->configurableProductBuilder->createConfigurableProduct($productId, false, false),
                'configuration' => null
            ];
        }

        // get configuration by type
        $configuration = null;
        switch ($query->getType()) {
            case 'guest': {
                $configuration = $this->guestConfigurationFinder->findById($query->getId());
                break;
            }
            case 'basket': {
                $configuration = $this->basketConfigurationFinder->findById($query->getId());
                break;
            }
            case 'order': {
                $configuration = $this->orderConfigurationFinder->findById($query->getId());
                break;
            }
            case 'code': {
                $configuration = $this->codeConfigurationFinder->findById($query->getId());
                break;
            }
            case 'immutable': {
                $configuration = $this->immutableConfigurationFinder->findById($query->getId());
                break;
            }
        }

        if (null === $configuration) {
            return null;
        }

        // set configuration properties
        /** @var State $state */
        $state = $this->aptoJsonSerializer->jsonUnSerialize($configuration['state']);
        $configuration['state'] =  $state->jsonSerialize();
        $configuration['productId'] = $configuration['product'][0]['id'];
        unset($configuration['product']);
        $configuration['type'] = $query->getType();

        // get product
        $product = $this->configurableProductBuilder->createConfigurableProduct($configuration['productId'], false, false);

        // set product immutable for immutable configurations
        if ($query->getType() === 'immutable') {
            $this->makeProductImmutable($product, $configuration);
        }

        // return result
        return [
            'product' => $product,
            'configuration' => $configuration
        ];
    }

    /**
     * @param FindConfigurableProductByConfiguration $query
     * @return mixed|null
     * @throws AptoJsonSerializerException
     * @throws CacheException
     * @throws InvalidArgumentException
     */
    public function handleFindConfigurableProductByConfiguration(FindConfigurableProductByConfiguration $query)
    {
        $configurationId = $query->getConfigurationId();
        $productId = $query->getProductId() ? $query->getProductId() : $this->defaultProduct;
        $product = $this->configurableProductBuilder->createConfigurableProduct($productId, false, false);

        if (!$product) {
            return null;
        }

        if ($query->getConfigurationType() === 'immutable') {
            $configuration = $this->immutableConfigurationFinder->findById($configurationId);
            $this->makeProductImmutable($product, $configuration);
        }

        return $product;
    }

    /**
     * @param array $product
     * @param array $configuration
     * @throws AptoJsonSerializerException
     */
    protected function makeProductImmutable(array &$product, array $configuration)
    {
        // get allowed Elements
        $allowedElements = $this->getAllowedElements($configuration);
        // clean up Rules
        $this->cleanUpRules($product, $allowedElements);
        // clean up Elements
        $this->cleanUpElements($product, $allowedElements);
    }

    /**
     * @param array $configuration
     * @return array
     * @throws AptoJsonSerializerException
     */
    protected function getAllowedElements(array $configuration)
    {
        /* @var $state State */
        $state = $this->aptoJsonSerializer->jsonUnSerialize($configuration['state']);

        if ($state instanceof State) {
            return $state->getStateWithoutParameters();
        }

        return [];
    }

    /**
     * @param array $product
     * @param array $allowedElements
     */
    protected function cleanUpRules(array &$product, array $allowedElements)
    {
        //Todo: Implement rules in Variants => implement rules here (if we can find a way to do so)
        $product['rules'] = [];
    }

    /**
     * @param array $product
     * @param array $allowedElements
     */
    protected function cleanUpElements(array &$product, array $allowedElements)
    {
        $mutableSectionIds = [];

        // @todo product sections are in old version of state
        foreach ($product['sections'] as $sKey => $section) {
            foreach ($section['elements'] as $key => $element) {
                // if section is present in product but not in allowedElemetens means sections must be mutable
                if (isset($allowedElements[$section['id']])) {
                    if (!array_key_exists($element['id'], $allowedElements[$section['id']])) {
                        unset($product['sections'][$sKey]['elements'][$key]);
                        continue;
                    }

                    // @todo check here $allowedElements is in new format
                    if (is_array($allowedElements[$section['id']][$element['id']])) {
                        $allowedElementValues = $allowedElements[$section['id']][$element['id']];
                        $product['sections'][$sKey]['elements'][$key] = $this->makeElementImmutable(
                            $element,
                            $allowedElementValues
                        );
                    }
                    unset($product['sections'][$sKey]['elements'][$key]['definitionObject']);
                } else {
                    $mutableSectionIds[$section['id']] = true;
                }
            }
            $product['sections'][$sKey]['elements'] = array_values($product['sections'][$sKey]['elements']);
        }
        // re-order sections, put alterable sections after immutable sections
        $alterableSectionsIds = $this->getAlterableSectionKeys($allowedElements);

        // re-oder sections, put mutable sections after alterable sections
        foreach ($mutableSectionIds as $sectionId=>$value) {
            $alterableSectionsIds[] = $sectionId;
        }

        $sections = [];
        $sectionsAlterable = [];

        foreach ($product['sections'] as $section) {
            if (in_array($section['id'], $alterableSectionsIds)) {
                $sectionsAlterable[] = $section;
            } else {
                $sections[] = $section;
            }
        }
        $product['sections'] = array_merge($sections, $sectionsAlterable);
    }

    /**
     * @param array $element
     * @param array $allowedElementValues
     * @return array
     */
    protected function makeElementImmutable(array $element, array $allowedElementValues)
    {
        /** @var ElementDefinition $elementDefinition */
        $elementDefinition = $element['definitionObject'];
        // TODO: handle non-default Elements in a generic way by replacing all selectable Values with "ElementImmutable"
        return $element;
    }

    /**
     * @param array $allowedElements
     * @return array
     */
    protected function getAlterableSectionKeys(array $allowedElements)
    {
        $keys = [];
        // @todo check here $allowedElements is in new format
        foreach ($allowedElements as $key => $section) {
            foreach ($section as $element) {
                if (is_array($element)) {
                    $keys[] = $key;
                }
            }
        }
        return $keys;
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindConfigurableProduct::class => [
            'method' => 'handleFindConfigurableProduct',
            'bus' => 'query_bus'
        ];

        yield FindProductConfiguration::class => [
            'method' => 'handleFindProductConfiguration',
            'bus' => 'query_bus'
        ];

        yield FindConfigurableProductByConfiguration::class => [
            'method' => 'handleFindConfigurableProductByConfiguration',
            'bus' => 'query_bus'
        ];
    }
}
