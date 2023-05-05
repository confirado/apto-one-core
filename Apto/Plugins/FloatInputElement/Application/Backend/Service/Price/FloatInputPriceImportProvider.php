<?php

namespace Apto\Plugins\FloatInputElement\Application\Backend\Service\Price;

use Apto\Base\Domain\Core\Model\AptoPrice\AptoPriceDuplicateException;
use Apto\Catalog\Application\Backend\Service\Price\AbstractPriceImportProvider;
use Apto\Catalog\Application\Backend\Service\Price\Exceptions\PriceTypeNotMatchingException;
use Apto\Catalog\Application\Backend\Service\Price\Exceptions\PriceTypeNotSetException;
use Apto\Catalog\Application\Backend\Service\Price\PriceItem;
use Apto\Plugins\FloatInputElement\Domain\Core\Model\FloatInputItem\FloatInputItemRepository;

class FloatInputPriceImportProvider extends AbstractPriceImportProvider
{
    const PRICE_TYPE = 'FloatInputElement';

    /**
     * @var FloatInputItemRepository
     */
    private $floatInputItemRepository;

    /**
     * ProductPriceImportProvider constructor.
     * @param FloatInputItemRepository $floatInputItemRepository
     * @throws PriceTypeNotSetException
     */
    public function __construct(FloatInputItemRepository $floatInputItemRepository)
    {
        parent::__construct();
        $this->floatInputItemRepository = $floatInputItemRepository;
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

        $floatInputItem = $this->floatInputItemRepository->findByElementId($priceItem->getEntityId()->getId());
        $floatInputItem->setAptoPricePrice($priceItem->getAptoPriceId(), $priceItem->getMoney());
        $this->floatInputItemRepository->update($floatInputItem);
        return;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->priceType;
    }
}