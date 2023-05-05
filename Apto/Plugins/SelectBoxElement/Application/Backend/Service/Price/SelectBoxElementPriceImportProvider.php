<?php

namespace Apto\Plugins\SelectBoxElement\Application\Backend\Service\Price;

use Apto\Base\Domain\Core\Model\AptoPrice\AptoPriceDuplicateException;
use Apto\Catalog\Application\Backend\Service\Price\AbstractPriceImportProvider;
use Apto\Catalog\Application\Backend\Service\Price\Exceptions\PriceTypeNotMatchingException;
use Apto\Catalog\Application\Backend\Service\Price\Exceptions\PriceTypeNotSetException;
use Apto\Catalog\Application\Backend\Service\Price\PriceItem;
use Apto\Plugins\SelectBoxElement\Domain\Core\Model\SelectBoxItem\SelectBoxItemRepository;

class SelectBoxElementPriceImportProvider extends AbstractPriceImportProvider
{
    const PRICE_TYPE = 'SelectBoxElement';

    /**
     * @var SelectBoxItemRepository
     */
    private $selectBoxItemRepository;

    /**
     * ProductPriceImportProvider constructor.
     * @param SelectBoxItemRepository $selectBoxItemRepository
     * @throws PriceTypeNotSetException
     */
    public function __construct(SelectBoxItemRepository $selectBoxItemRepository)
    {
        parent::__construct();
        $this->selectBoxItemRepository = $selectBoxItemRepository;
    }

    /**
     * @param PriceItem $priceItem
     * @throws AptoPriceDuplicateException
     * @throws PriceTypeNotMatchingException
     */
    public function setPrice(PriceItem $priceItem): void
    {
        if ($priceItem->getPriceType() !== $this->priceType) {
            throw new PriceTypeNotMatchingException($priceItem->getPriceType(), $this->priceType);
        }

        $selectBoxItem = $this->selectBoxItemRepository->findById($priceItem->getEntityId()->getId());
        $selectBoxItem->setAptoPricePrice($priceItem->getAptoPriceId(), $priceItem->getMoney());
        $this->selectBoxItemRepository->update($selectBoxItem);
    }
}