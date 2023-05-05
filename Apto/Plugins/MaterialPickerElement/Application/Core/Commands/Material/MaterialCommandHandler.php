<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Core\Commands\Material;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material\MaterialRepository;

class MaterialCommandHandler extends AbstractCommandHandler
{
    /**
     * @var MaterialRepository
     */
    protected $materialRepository;

    /**
     * @param MaterialRepository $materialRepository
     */
    public function __construct(MaterialRepository $materialRepository)
    {
        $this->materialRepository = $materialRepository;
    }

    /**
     * @param IncrementMaterialClicks $command
     */
    public function handleIncrementMaterialClicks(IncrementMaterialClicks $command)
    {
        $material = $this->materialRepository->findById($command->getId());
        if (null !== $material) {
            $material->incrementClicks();
            $this->materialRepository->update($material);
            //$material->publishEvents();
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield IncrementMaterialClicks::class => [
            'method' => 'handleIncrementMaterialClicks',
            'bus' => 'command_bus',
            'aptoMessageName' => 'IncrementMaterialPickerMaterialClicks'
        ];
    }
}