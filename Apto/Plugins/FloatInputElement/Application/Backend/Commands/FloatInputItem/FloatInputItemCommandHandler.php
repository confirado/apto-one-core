<?php

namespace Apto\Plugins\FloatInputElement\Application\Backend\Commands\FloatInputItem;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Base\Domain\Core\Model\AptoPrice\AptoPriceDuplicateException;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Plugins\FloatInputElement\Domain\Core\Model\FloatInputItem\FloatInputItem;
use Apto\Plugins\FloatInputElement\Domain\Core\Model\FloatInputItem\FloatInputItemRepository;
use Money\Currency;
use Money\Money;

class FloatInputItemCommandHandler extends AbstractCommandHandler
{
    /**
     * @var FloatInputItemRepository
     */
    protected $floatInputItemRepository;

    /**
     * FloatInputItemCommandHandler constructor.
     * @param FloatInputItemRepository $floatInputItemRepository
     */
    public function __construct(FloatInputItemRepository $floatInputItemRepository)
    {
        $this->floatInputItemRepository = $floatInputItemRepository;
    }

    /**
     * @param AddFloatInputPrice $command
     * @throws InvalidUuidException
     * @throws AptoPriceDuplicateException
     */
    public function handleAddFloatInputPrice(AddFloatInputPrice $command)
    {
        // find or create a FloatInputItem
        $floatInputItem = $this->floatInputItemRepository->findByElementId($command->getElementId());
        if (null === $floatInputItem) {
            $floatInputItem = new FloatInputItem(
                $this->floatInputItemRepository->nextIdentity(),
                new AptoUuid($command->getProductId()),
                new AptoUuid($command->getSectionId()),
                new AptoUuid($command->getElementId())
            );
            $this->floatInputItemRepository->add($floatInputItem);
        }

        // add price
        $floatInputItem->addAptoPrice(
            new Money(
                $command->getAmount(),
                new Currency($command->getCurrency())
            ),
            new AptoUuid(
                $command->getCustomerGroupId()
            )
        );

        $floatInputItem->publishEvents();
    }

    /**
     * @param RemoveFloatInputPrice $command
     * @throws InvalidUuidException
     */
    public function handleRemoveFloatInputPrice(RemoveFloatInputPrice $command)
    {
        $floatInputItem = $this->floatInputItemRepository->findByElementId($command->getElementId());

        if (null !== $floatInputItem) {
            $floatInputItem->removeAptoPrice(
                new AptoUuid(
                    $command->getPriceId()
                )
            );
            $this->floatInputItemRepository->update($floatInputItem);
            $floatInputItem->publishEvents();
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddFloatInputPrice::class => [
            'method' => 'handleAddFloatInputPrice',
            'bus' => 'command_bus'
        ];

        yield RemoveFloatInputPrice::class => [
            'method' => 'handleRemoveFloatInputPrice',
            'bus' => 'command_bus'
        ];
    }
}