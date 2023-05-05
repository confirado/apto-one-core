<?php

namespace Apto\Plugins\PricePerUnitElement\Application\Backend\Service\Price;

use Apto\Base\Domain\Core\Model\AptoPrice\AptoPriceDuplicateException;
use Apto\Catalog\Application\Backend\Service\Price\AbstractPriceImportProvider;
use Apto\Catalog\Application\Backend\Service\Price\Exceptions\PriceTypeNotMatchingException;
use Apto\Catalog\Application\Backend\Service\Price\Exceptions\PriceTypeNotSetException;
use Apto\Catalog\Application\Backend\Service\Price\PriceItem;
use Apto\Plugins\PricePerUnitElement\Domain\Core\Model\PricePerUnitItem\PricePerUnitItemRepository;

class PricePerUnitPriceImportProvider extends AbstractPriceImportProvider
{
    const PRICE_TYPE = 'PricePerUnit';

    /**
     * @var PricePerUnitItemRepository
     */
    private $pricePerUnitItemRepository;

    /**
     * ProductPriceImportProvider constructor.
     * @param PricePerUnitItemRepository $pricePerUnitItemRepository
     * @throws PriceTypeNotSetException
     */
    public function __construct(PricePerUnitItemRepository $pricePerUnitItemRepository)
    {
        parent::__construct();
        $this->pricePerUnitItemRepository = $pricePerUnitItemRepository;
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

        $pricePerUnitItem = $this->pricePerUnitItemRepository->findByElementId($priceItem->getEntityId()->getId());
        $pricePerUnitItem->setAptoPricePrice($priceItem->getAptoPriceId(), $priceItem->getMoney());
        $this->pricePerUnitItemRepository->update($pricePerUnitItem);
    }
}