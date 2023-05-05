<?php

namespace Apto\Catalog\Domain\Core\Factory\ConfigurableProduct;

use Apto\Base\Domain\Core\Service\AptoJsonSerializerException;
use Apto\Catalog\Application\Core\Service\ConfigurableProduct\ConfigurableProductBuilder;
use Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm\ProductOrmRepository;

class ConfigurableProductFactory
{
    /**
     * @var ConfigurableProductBuilder
     */
    protected $configurableProductBuilder;

    /**
     * @var ProductOrmRepository
     */
    protected $productRepository;

    /**
     * @param ConfigurableProductBuilder $configurableProductBuilder
     * @param ProductOrmRepository $productRepository
     */
    public function __construct(ConfigurableProductBuilder $configurableProductBuilder, ProductOrmRepository $productRepository)
    {
        $this->configurableProductBuilder = $configurableProductBuilder;
        $this->productRepository = $productRepository;
    }

    /**
     * @param string $productIdOrSeoUrl
     * @param bool $keepDefinitions
     * @param bool $withRules
     * @param bool $withComputedValues
     * @return ConfigurableProduct|null
     * @throws AptoJsonSerializerException
     * @throws \Apto\Base\Domain\Core\Model\InvalidUuidException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function fromProductId(string $productIdOrSeoUrl, bool $keepDefinitions = true, bool $withRules = true, bool $withComputedValues = true): ?ConfigurableProduct
    {
        $configurableProduct = $this->configurableProductBuilder->createConfigurableProduct($productIdOrSeoUrl, $keepDefinitions, $withRules, $withComputedValues);

        if ($configurableProduct === null || !array_key_exists('id', $configurableProduct)) {
            return null;
        }
        $product = $this->productRepository->findById($configurableProduct['id']);
        return $configurableProduct ? new ConfigurableProduct($configurableProduct, $product) : null;
    }

}
