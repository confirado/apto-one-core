<?php

namespace Apto\Catalog\Application\Backend\Commands\PriceMatrix;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventPublisher;
use Apto\Catalog\Domain\Core\Model\PriceMatrix\PriceMatrix;
use Apto\Catalog\Domain\Core\Model\PriceMatrix\PriceMatrixPosition;
use Apto\Catalog\Domain\Core\Model\PriceMatrix\PriceMatrixRemoved;
use Apto\Catalog\Domain\Core\Model\PriceMatrix\PriceMatrixRepository;
use Money\Currency;
use Money\Money;

class PriceMatrixCommandHandler extends AbstractCommandHandler
{
    /**
     * @var PriceMatrixRepository
     */
    private $priceMatrixRepository;

    /**
     * PriceMatrixCommandHandler constructor.
     * @param PriceMatrixRepository $priceMatrixRepository
     */
    public function __construct(PriceMatrixRepository $priceMatrixRepository)
    {
        $this->priceMatrixRepository = $priceMatrixRepository;
    }

    /**
     * @param AddPriceMatrix $command
     */
    public function handleAddPriceMatrix(AddPriceMatrix $command)
    {
        $priceMatrix = new PriceMatrix(
            $this->priceMatrixRepository->nextIdentity(),
            $this->getTranslatedValue($command->getName())
        );

        $this->priceMatrixRepository->add($priceMatrix);
        $priceMatrix->publishEvents();
    }

    /**
     * @param UpdatePriceMatrix $command
     */
    public function handleUpdatePriceMatrix(UpdatePriceMatrix $command)
    {
        $priceMatrix = $this->priceMatrixRepository->findById($command->getId());

        if (null !== $priceMatrix) {
            $priceMatrix->setName(
                $this->getTranslatedValue($command->getName())
            );
            $priceMatrix->publishEvents();
        }
    }

    /**
     * @param RemovePriceMatrix $command
     */
    public function handleRemovePriceMatrix(RemovePriceMatrix $command)
    {
        $priceMatrix = $this->priceMatrixRepository->findById($command->getId());

        if (null !== $priceMatrix) {
            $this->priceMatrixRepository->remove($priceMatrix);
            DomainEventPublisher::instance()->publish(
                new PriceMatrixRemoved(
                    $priceMatrix->getId()
                )
            );
        }
    }

    /**
     * @param AddPriceMatrixElement $command
     */
    public function handleAddPriceMatrixElement(AddPriceMatrixElement $command)
    {
        $priceMatrix = $this->priceMatrixRepository->findById($command->getPriceMatrixId());

        if (null !== $priceMatrix) {
            $priceMatrix->addPriceMatrixElement(
                new PriceMatrixPosition(
                    $command->getColumnValue(),
                    $command->getRowValue()
                )
            );
            $priceMatrix->publishEvents();
        }
    }

    /**
     * @param RemovePriceMatrixElement $command
     */
    public function handleRemovePriceMatrixElement(RemovePriceMatrixElement $command)
    {
        $priceMatrix = $this->priceMatrixRepository->findById($command->getPriceMatrixId());

        if (null !== $priceMatrix) {
            $priceMatrix->removePriceMatrixElement(
                new AptoUuid($command->getPriceMatrixElementId())
            );
            $priceMatrix->publishEvents();
        }
    }

    /**
     * @param AddPriceMatrixElementPrice $command
     */
    public function handleAddPriceMatrixElementPrice(AddPriceMatrixElementPrice $command)
    {
        $priceMatrix = $this->priceMatrixRepository->findById($command->getPriceMatrixId());

        if (null !== $priceMatrix) {
            $priceMatrix->addPriceMatrixElementPrice(
                new AptoUuid($command->getPriceMatrixElementId()),
                new Money(
                    $command->getAmount(),
                    new Currency(
                        $command->getCurrency()
                    )
                ),
                new AptoUuid($command->getCustomerGroupId())
            );
            $priceMatrix->publishEvents();
        }
    }

    /**
     * @param RemovePriceMatrixElementPrice $command
     */
    public function handleRemovePriceMatrixElementPrice(RemovePriceMatrixElementPrice $command)
    {
        $priceMatrix = $this->priceMatrixRepository->findById($command->getPriceMatrixId());

        if (null !== $priceMatrix) {
            $priceMatrix->removePriceMatrixElementPrice(
                new AptoUuid($command->getPriceMatrixElementId()),
                new AptoUuid($command->getPriceMatrixElementPriceId())
            );
            $priceMatrix->publishEvents();
        }
    }

    /**
     * @param AddPriceMatrixElementCustomProperty $command
     */
    public function handleAddPriceMatrixElementCustomProperty(AddPriceMatrixElementCustomProperty $command)
    {
        $priceMatrix = $this->priceMatrixRepository->findById($command->getPriceMatrixId());

        if (null !== $priceMatrix) {
            $priceMatrix->addPriceMatrixElementCustomProperty(
                new AptoUuid($command->getPriceMatrixElementId()),
                $command->getKey(),
                $command->getValue(),
                $command->getTranslatable()
            );
            $priceMatrix->publishEvents();
        }
    }

    /**
     * @param RemovePriceMatrixElementCustomProperty $command
     */
    public function handleRemovePriceMatrixElementCustomProperty(RemovePriceMatrixElementCustomProperty $command)
    {
        $priceMatrix = $this->priceMatrixRepository->findById($command->getPriceMatrixId());

        if (null !== $priceMatrix) {
            $priceMatrix->removePriceMatrixElementCustomProperty(
                new AptoUuid($command->getPriceMatrixElementId()),
                new AptoUuid($command->getId())
            );
            $priceMatrix->publishEvents();
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddPriceMatrix::class => [
            'method' => 'handleAddPriceMatrix',
            'bus' => 'command_bus'
        ];

        yield UpdatePriceMatrix::class => [
            'method' => 'handleUpdatePriceMatrix',
            'bus' => 'command_bus'
        ];

        yield RemovePriceMatrix::class => [
            'method' => 'handleRemovePriceMatrix',
            'bus' => 'command_bus'
        ];

        yield AddPriceMatrixElement::class => [
            'method' => 'handleAddPriceMatrixElement',
            'bus' => 'command_bus'
        ];

        yield RemovePriceMatrixElement::class => [
            'method' => 'handleRemovePriceMatrixElement',
            'bus' => 'command_bus'
        ];

        yield AddPriceMatrixElementPrice::class => [
            'method' => 'handleAddPriceMatrixElementPrice',
            'bus' => 'command_bus'
        ];

        yield RemovePriceMatrixElementPrice::class => [
            'method' => 'handleRemovePriceMatrixElementPrice',
            'bus' => 'command_bus'
        ];

        yield AddPriceMatrixElementCustomProperty::class => [
            'method' => 'handleAddPriceMatrixElementCustomProperty',
            'bus' => 'command_bus'
        ];

        yield RemovePriceMatrixElementCustomProperty::class => [
            'method' => 'handleRemovePriceMatrixElementCustomProperty',
            'bus' => 'command_bus'
        ];
    }
}
