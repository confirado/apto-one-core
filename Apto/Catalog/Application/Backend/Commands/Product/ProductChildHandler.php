<?php

namespace Apto\Catalog\Application\Backend\Commands\Product;

use Exception;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;
use Apto\Catalog\Domain\Core\Model\Product\ProductRepository;

abstract class ProductChildHandler extends AbstractCommandHandler
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * ProductChildHandler constructor.
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param string|null $identifierValue
     * @param AptoTranslatedValue $aptoTranslatedValue
     * @return Identifier|null
     * @throws Exception
     */
    protected function getIdentifier(?string $identifierValue, AptoTranslatedValue $aptoTranslatedValue): ?Identifier
    {
        if (null !== $identifierValue) {
            return new Identifier($identifierValue);
        }

        foreach ($aptoTranslatedValue->jsonSerialize() as $item) {
            return new Identifier($item);
        }

        return null;
    }
}