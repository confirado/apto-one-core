<?php

namespace Apto\Plugins\PartsList\Application\Backend\Commands\Unit;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Base\Domain\Core\Model\InvalidTranslatedValueException;
use Apto\Plugins\PartsList\Domain\Core\Model\Unit\Unit;
use Apto\Plugins\PartsList\Domain\Core\Model\Unit\UnitRepository;
use Apto\Base\Domain\Core\Model\InvalidUuidException;

class UnitCommandHandler extends AbstractCommandHandler
{
    /**
     * @var UnitRepository
     */
    private $unitRepository;

    /**
     * UnitCommandHandler constructor.
     * @param UnitRepository $unitRepository
     */
    public function __construct(UnitRepository $unitRepository)
    {
        $this->unitRepository = $unitRepository;
    }

    /**
     * @param AddUnit $command
     * @throws InvalidTranslatedValueException
     * @throws InvalidUuidException
     */
    public function handleAddUnit(AddUnit $command)
    {
        // create new unit
        $unit = new Unit(
            $this->unitRepository->nextIdentity(),
            $command->getUnit()
        );

        // add unit and publish fired events
        $this->unitRepository->add($unit);
        $unit->publishEvents();
    }

    /**
     * @param UpdateUnit $command
     */
    public function handleUpdateUnit(UpdateUnit $command)
    {
        // find unit to update
        $unit = $this->unitRepository->findById($command->getId());

        // return if now unit was found
        if (null === $unit) {
            return;
        }

        // update unit properties
        $unit->setUnit($command->getUnit());

        // update unit and publish fired events
        $this->unitRepository->update($unit);
        $unit->publishEvents();
    }

    /**
     * @param RemoveUnit $command
     */
    public function handleRemoveUnit(RemoveUnit $command)
    {
        $unit = $this->unitRepository->findById($command->getId());

        if (null === $unit) {
            return;
        }

        $this->unitRepository->remove($unit);
    }

    public static function getHandledMessages(): iterable
    {
        yield AddUnit::class => [
            'method' => 'handleAddUnit',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'command_bus'
        ];

        yield UpdateUnit::class => [
            'method' => 'handleUpdateUnit',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'command_bus'
        ];

        yield RemoveUnit::class => [
            'method' => 'handleRemoveUnit',
            'aptoMessagePrefix' => 'AptoPartsList',
            'bus' => 'command_bus'
        ];
    }
}