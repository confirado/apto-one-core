<?php

namespace Apto\Plugins\PricePerUnitElement\Application\Backend\Commands\PricePerUnitItem;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Plugins\PricePerUnitElement\Domain\Core\Model\PricePerUnitItem\PricePerUnitItem;
use Apto\Plugins\PricePerUnitElement\Domain\Core\Model\PricePerUnitItem\PricePerUnitItemRepository;
use Money\Currency;
use Money\Money;

class PricePerUnitItemCommandHandler extends AbstractCommandHandler
{
    /**
     * @var PricePerUnitItemRepository
     */
    protected $pricePerUnitItemRepository;

    /**
     * PricePerUnitItemCommandHandler constructor.
     * @param PricePerUnitItemRepository $pricePerUnitItemRepository
     */
    public function __construct(PricePerUnitItemRepository $pricePerUnitItemRepository)
    {
        $this->pricePerUnitItemRepository = $pricePerUnitItemRepository;
    }

    /**
     * @param AddPricePerUnitPrice $command
     */
    public function handleAddPricePerUnitPrice(AddPricePerUnitPrice $command)
    {
        // find or create a PricePerUnitItem
        $pricePerUnitItem = $this->pricePerUnitItemRepository->findByElementId($command->getElementId());
        if (null === $pricePerUnitItem) {
            $pricePerUnitItem = new PricePerUnitItem(
                $this->pricePerUnitItemRepository->nextIdentity(),
                new AptoUuid($command->getProductId()),
                new AptoUuid($command->getSectionId()),
                new AptoUuid($command->getElementId())
            );
            $this->pricePerUnitItemRepository->add($pricePerUnitItem);
        }

        // add price
        $pricePerUnitItem->addAptoPrice(
            new Money(
                $command->getAmount(),
                new Currency($command->getCurrency())
            ),
            new AptoUuid(
                $command->getCustomerGroupId()
            )
        );

        $pricePerUnitItem->publishEvents();
    }

    /**
     * @param RemovePricePerUnitPrice $command
     */
    public function handleRemovePricePerUnitPrice(RemovePricePerUnitPrice $command)
    {
        $pricePerUnitItem = $this->pricePerUnitItemRepository->findByElementId($command->getElementId());

        if (null !== $pricePerUnitItem) {
            $pricePerUnitItem->removeAptoPrice(
                new AptoUuid(
                    $command->getPriceId()
                )
            );
            $this->pricePerUnitItemRepository->update($pricePerUnitItem);
            $pricePerUnitItem->publishEvents();
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddPricePerUnitPrice::class => [
            'method' => 'handleAddPricePerUnitPrice',
            'bus' => 'command_bus'
        ];

        yield RemovePricePerUnitPrice::class => [
            'method' => 'handleRemovePricePerUnitPrice',
            'bus' => 'command_bus'
        ];
    }
}