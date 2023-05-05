<?php

namespace Apto\Catalog\Application\Backend\Service\Price;

use Apto\Base\Domain\Core\Model\AptoPrice\AptoPriceDuplicateException;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Backend\Service\Price\Exceptions\PriceTypeNotMatchingException;
use Apto\Catalog\Application\Backend\Service\Price\Exceptions\PriceTypeNotSetException;
use Apto\Catalog\Domain\Core\Model\PriceMatrix\PriceMatrixPosition;
use Apto\Catalog\Domain\Core\Model\PriceMatrix\PriceMatrixRepository;

class PriceMatrixPriceImportProvider extends AbstractPriceImportProvider
{
    const PRICE_TYPE = 'PriceMatrix';

    /**
     * @var PriceMatrixRepository
     */
    private $priceMatrixRepository;

    /**
     * PriceMatrixPriceImportProvider constructor.
     * @param PriceMatrixRepository $priceMatrixRepository
     * @throws PriceTypeNotSetException
     */
    public function __construct(PriceMatrixRepository $priceMatrixRepository)
    {
        parent::__construct();
        $this->priceMatrixRepository = $priceMatrixRepository;
    }

    /**
     * @param PriceItem $priceItem
     * @throws PriceTypeNotMatchingException
     * @throws AptoPriceDuplicateException
     * @throws InvalidUuidException
     */
    public function setPrice(PriceItem $priceItem): void
    {
        if ($priceItem->getPriceType() !== $this->priceType) {
            throw new PriceTypeNotMatchingException($priceItem->getPriceType(), $this->priceType);
        }

        $priceMatrix = $this->priceMatrixRepository->findById($priceItem->getEntityId()->getId());
        $fieldArray = explode('_', $priceItem->getFieldName());
        $column = $fieldArray[1];
        $row = $fieldArray[2];
        $elementId = $priceMatrix->getPriceMatrixElementIdByPosition(new PriceMatrixPosition($column, $row));
        $priceMatrix->removePriceMatrixElementPrice($elementId, $priceItem->getAptoPriceId());
        $priceMatrix->addPriceMatrixElementPrice($elementId, $priceItem->getMoney(), $priceItem->getCustomerGroup());

        $this->priceMatrixRepository->update($priceMatrix);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->priceType;
    }
}