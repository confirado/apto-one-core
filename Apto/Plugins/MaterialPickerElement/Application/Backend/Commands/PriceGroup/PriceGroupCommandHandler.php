<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\PriceGroup;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventPublisher;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\PriceGroup\PriceGroup;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\PriceGroup\PriceGroupRemoved;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\PriceGroup\PriceGroupRepository;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\PriceGroup\PriceMatrix;

class PriceGroupCommandHandler extends AbstractCommandHandler
{
    /**
     * @var PriceGroupRepository
     */
    protected $priceGroupRepository;

    /**
     * PriceGroupCommandHandler constructor.
     * @param PriceGroupRepository $priceGroupRepository
     */
    public function __construct(PriceGroupRepository $priceGroupRepository)
    {
        $this->priceGroupRepository = $priceGroupRepository;
    }

    /**
     * @param AddPriceGroup $command
     * @return void
     * @throws InvalidUuidException
     */
    public function handleAddPriceGroup(AddPriceGroup $command)
    {
        $priceMatrix = $command->getPriceMatrix();
        $priceGroup = new PriceGroup(
            $this->priceGroupRepository->nextIdentity(),
            $this->getTranslatedValue($command->getName()),
            $this->getTranslatedValue($command->getInternalName()),
            $command->getAdditionalCharge(),
            is_array($priceMatrix) ? PriceMatrix::fromArray($priceMatrix) : null
        );

        $this->priceGroupRepository->add($priceGroup);
        $priceGroup->publishEvents();
    }

    /**
     * @param UpdatePriceGroup $command
     * @return void
     */
    public function handleUpdatePriceGroup(UpdatePriceGroup $command)
    {
        $priceGroup = $this->priceGroupRepository->findById($command->getId());

        if (null !== $priceGroup) {
            $priceMatrix = $command->getPriceMatrix();
            if (is_array($priceMatrix)) {
                $priceMatrix = PriceMatrix::fromArray($priceMatrix);
            } else {
                $priceMatrix = new PriceMatrix();
            }

            $priceGroup
                ->setName($this->getTranslatedValue($command->getName()))
                ->setInternalName($this->getTranslatedValue($command->getInternalName()))
                ->setAdditionalCharge($command->getAdditionalCharge())
                ->setPriceMatrix($priceMatrix);

            $this->priceGroupRepository->update($priceGroup);
            $priceGroup->publishEvents();
        }
    }

    /**
     * @param RemovePriceGroup $command
     * @return void
     */
    public function handleRemovePriceGroup(RemovePriceGroup $command)
    {
        $priceGroup = $this->priceGroupRepository->findById($command->getId());

        if (null !== $priceGroup) {
            $this->priceGroupRepository->remove($priceGroup);
            DomainEventPublisher::instance()->publish(
                new PriceGroupRemoved(
                    $priceGroup->getId()
                )
            );
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddPriceGroup::class => [
            'method' => 'handleAddPriceGroup',
            'bus' => 'command_bus',
            'aptoMessageName' => 'AddMaterialPickerPriceGroup'
        ];

        yield UpdatePriceGroup::class => [
            'method' => 'handleUpdatePriceGroup',
            'bus' => 'command_bus',
            'aptoMessageName' => 'UpdateMaterialPickerPriceGroup'
        ];

        yield RemovePriceGroup::class => [
            'method' => 'handleRemovePriceGroup',
            'bus' => 'command_bus',
            'aptoMessageName' => 'RemoveMaterialPickerPriceGroup'
        ];
    }
}