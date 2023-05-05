<?php

namespace Apto\Catalog\Application\Core\Service\ConfigurableProduct;

use Apto\Base\Application\Core\Service\AptoCache\AptoCacheService;
use Apto\Base\Application\Core\Service\RequestStore;
use Apto\Base\Domain\Core\Model\FileSystem\Directory\Directory;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Service\AptoJsonSerializer;
use Apto\Base\Domain\Core\Service\AptoJsonSerializerException;
use Apto\Catalog\Application\Core\Query\Product\ProductFinder;
use Apto\Catalog\Application\Core\Query\Shop\ShopFinder;
use Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry\ElementDefinitionRegistry;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementDefinition;
use Apto\Catalog\Domain\Core\Model\Product\Element\ElementValueCollection;

class ConfigurableProductBuilder
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
     * @var MediaFileSystemConnector
     */
    private $fileSystemConnector;

    /**
     * @var ShopFinder
     */
    private $shopFinder;

    /**
     * @var RequestStore
     */
    private $requestStore;

    /**
     * @var ElementDefinitionRegistry
     */
    private $elementDefinitionRegistry;

    /**
     * @param ProductFinder $productFinder
     * @param AptoJsonSerializer $aptoJsonSerializer
     * @param MediaFileSystemConnector $fileSystemConnector
     * @param ShopFinder $shopFinder
     * @param RequestStore $requestStore
     * @param ElementDefinitionRegistry $elementDefinitionRegistry
     */
    public function __construct(
        ProductFinder $productFinder,
        AptoJsonSerializer $aptoJsonSerializer,
        MediaFileSystemConnector $fileSystemConnector,
        ShopFinder $shopFinder,
        RequestStore $requestStore,
        ElementDefinitionRegistry $elementDefinitionRegistry
    ) {
        $this->productFinder = $productFinder;
        $this->aptoJsonSerializer = $aptoJsonSerializer;
        $this->fileSystemConnector = $fileSystemConnector;
        $this->shopFinder = $shopFinder;
        $this->requestStore = $requestStore;
        $this->elementDefinitionRegistry = $elementDefinitionRegistry;
    }

    /**
     * @param string $productId
     * @param bool $keepDefinitions
     * @param bool $withRules
     * @param bool $withComputedValues
     * @return mixed
     * @throws AptoJsonSerializerException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function createConfigurableProduct(string $productId, bool $keepDefinitions = false, bool $withRules = true, bool $withComputedValues = true)
    {
        $host = $this->requestStore->getHttpHost();
        $shop = $this->shopFinder->findByDomain($host);

        $productCacheKey = 'Configurable-Product-';
        $productCacheKey .= $withRules ? 'wRules-' : 'woRules-';
        $productCacheKey .= $withComputedValues ? 'wComputedValues-' : 'woComputedValues-';
        $productCacheKey .= $productId . '-' . $host;

        $product = AptoCacheService::getItem($productCacheKey);
        if ($product) {
            $product['cached'] = true;
            return $product;
        }

        $product = $this->productFinder->findConfigurableProductById($productId, $withRules, $withComputedValues);
        if (null === $product) {
            $product = $this->productFinder->findConfigurableProductBySeoUrl($productId, $withRules, $withComputedValues);
        }

        if (!$this->hasProductShop($product, $shop)) {
            return null;
        }

        $domainProperties = $this->getCurrentDomainProperties($product, $shop);
        if (isset($domainProperties) && $domainProperties['previewImage'] && $domainProperties['previewImageMediaFile']) {
            $product['previewImage'] = $domainProperties['previewImage'];
            $product['previewImageMediaFile'] = $domainProperties['previewImageMediaFile'];
        }

        if (isset($product['sections'])) {
            foreach ($product['sections'] as $iSection => &$section) {
                // set section previewImage
                if (isset($section['previewImage'][0])) {
                    $mediaFile = $section['previewImage'][0];
                    $file = new File(new Directory($mediaFile['path']), $mediaFile['filename'] . '.' . $mediaFile['extension']);
                    $section['previewImage'] = [
                        'mediaFile' => $mediaFile,
                        'fileUrl' => $this->fileSystemConnector->getFileUrl($file)
                    ];
                } else {
                    $section['previewImage'] = null;
                }

                if (isset($section['elements'])) {
                    foreach ($section['elements'] as $iElement => &$element) {
                        // set element definition
                        /** @var ElementDefinition $elementDefinition */
                        $elementDefinition = $this->aptoJsonSerializer->jsonUnSerialize($element['definition']);

                        $definition = [];
                        $definition['name'] = $elementDefinition::getName();
                        $definition['component'] = $elementDefinition::getFrontendComponent();
                        $definition['staticValues'] = $this->elementDefinitionRegistry->getStaticValuesProvider(get_class($elementDefinition))->getStaticValues($elementDefinition);

                        $selectableValues = $elementDefinition->getSelectableValues();
                        /** @var ElementValueCollection $selectableValue */
                        foreach ($selectableValues as $selectableProperty => $selectableValue) {
                            $definition['properties'][$selectableProperty] = $selectableValue->jsonSerialize();
                        }
                        if ($keepDefinitions) {
                            $element['definitionObject'] = $elementDefinition;
                        }
                        $element['definition'] = $definition;

                        // set element previewImage
                        if (isset($element['previewImage'][0])) {
                            $mediaFile = $element['previewImage'][0];
                            $file = new File(new Directory($mediaFile['path']), $mediaFile['filename'] . '.' . $mediaFile['extension']);
                            $element['previewImage'] = [
                                'mediaFile' => $mediaFile,
                                'fileUrl' => $this->fileSystemConnector->getFileUrl($file)
                            ];
                        } else {
                            $element['previewImage'] = null;
                        }

                        foreach ($element['attachments'] as &$attachment) {
                            if (isset($attachment['mediaFile'][0])) {
                                $mediaFile = $attachment['mediaFile'][0];
                                $file = new File(new Directory($mediaFile['path']), $mediaFile['filename'] . '.' . $mediaFile['extension']);
                                $attachment['fileUrl'] = $file->getPath();
                                $attachment['mediaFile'] = $mediaFile;
                            } else {
                                $attachment['fileUrl'] = null;
                                $attachment['mediaFile'] = null;
                            }
                        }
                    }
                }
            }
        }

        AptoCacheService::setItem($productCacheKey, $product);
        $product['cached'] = false;
        return $product;
    }

    /**
     * @param array $product
     * @param array $shop
     * @return array|null
     */
    private function getCurrentDomainProperties(array $product, array $shop): ?array
    {
        foreach ($product['domainProperties'] as $domainProperties) {
            if ($domainProperties['shop']['id'] === $shop['id']) {
                return $domainProperties;
            }
        }
        return null;
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
}
