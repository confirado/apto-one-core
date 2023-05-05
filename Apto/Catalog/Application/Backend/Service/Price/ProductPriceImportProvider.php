<?php

namespace Apto\Catalog\Application\Backend\Service\Price;

use Apto\Base\Domain\Core\Model\AptoPrice\AptoPriceDuplicateException;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Backend\Service\Price\Exceptions\PriceTypeNotSetException;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;
use Apto\Catalog\Domain\Core\Model\Product\ProductRepository;

class ProductPriceImportProvider extends AbstractPriceImportProvider
{
    const PRICE_TYPE = 'Product';

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * ProductPriceImportProvider constructor.
     * @param ProductRepository $productRepository
     * @throws PriceTypeNotSetException
     */
    public function __construct(ProductRepository $productRepository)
    {
        parent::__construct();
        $this->productRepository = $productRepository;
    }

    /**
     * @param PriceItem $priceItem
     * @throws AptoPriceDuplicateException
     * @throws Exceptions\PriceTypeNotMatchingException
     * @throws InvalidUuidException
     * @throws \Exception
     */
    public function setPrice(PriceItem $priceItem): void
    {
        if ($priceItem->getPriceType() !== $this->priceType) {
            throw new Exceptions\PriceTypeNotMatchingException($priceItem->getPriceType(), $this->priceType);
        }

        $fieldName = $priceItem->getFieldName();
        $fieldArray = explode('_', $fieldName);
        $product = $this->productRepository->findByIdentifier(new Identifier($fieldArray[0]));
        // Product Price
        if (sizeof($fieldArray) === 1) {
            $product->setAptoPricePrice($priceItem->getAptoPriceId(), $priceItem->getMoney());
            $this->productRepository->update($product);
            return;
        }

        // Section Price
        $sectionId = $product->getSectionIdByIdentifier(new Identifier($fieldArray[2]));
        if ($fieldArray[1] === 'PS') {
            $product->removeSectionPrice($sectionId, $priceItem->getAptoPriceId());
            $product->addSectionPrice($sectionId, $priceItem->getMoney(), new AptoUuid($priceItem->getCustomerGroup()));
            $this->productRepository->update($product);
            return;
        }
        // Section Price
        $elementId = $product->getElementIdByIdentifier($sectionId, new Identifier($fieldArray[3]));
        if ($fieldArray[1] === 'PSE') {
             $product->removeElementPrice($sectionId, $elementId, $priceItem->getAptoPriceId());
             $product->addElementPrice($sectionId, $elementId, $priceItem->getMoney(), $priceItem->getCustomerGroup());
        }
        $this->productRepository->update($product);
    }
}