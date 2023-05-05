<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Pool;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventPublisher;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material\MaterialRepository;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Pool\Pool;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Pool\PoolRemoved;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Pool\PoolRepository;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\PriceGroup\PriceGroupRepository;

class PoolCommandHandler extends AbstractCommandHandler
{
    /**
     * @var PoolRepository
     */
    protected $poolRepository;

    /**
     * @var MaterialRepository
     */
    private $materialRepository;

    /**
     * @var PriceGroupRepository
     */
    private $priceGroupRepository;

    /**
     * @param PoolRepository $poolRepository
     * @param MaterialRepository $materialRepository
     * @param PriceGroupRepository $priceGroupRepository
     */
    public function __construct(PoolRepository $poolRepository, MaterialRepository $materialRepository, PriceGroupRepository $priceGroupRepository)
    {
        $this->poolRepository = $poolRepository;
        $this->materialRepository = $materialRepository;
        $this->priceGroupRepository = $priceGroupRepository;
    }

    /**
     * @param AddPool $command
     * @return void
     * @throws InvalidUuidException
     */
    public function handleAddPool(AddPool $command)
    {
        $pool = new Pool(
            $this->poolRepository->nextIdentity(),
            $this->getTranslatedValue($command->getName())
        );

        $this->poolRepository->add($pool);
        $pool->publishEvents();
    }

    /**
     * @param AddPoolItem $command
     * @return void
     */
    public function handleAddPoolItem(AddPoolItem $command)
    {
        $pool = $this->poolRepository->findById($command->getPoolId());

        if (null !== $pool) {
            $material = $this->materialRepository->findById($command->getMaterialId());
            $priceGroup = $this->priceGroupRepository->findById($command->getPriceGroupId());

            if (null !== $material && null !== $priceGroup) {
                $pool->addItem($material, $priceGroup);
                $this->poolRepository->update($pool);
                $pool->publishEvents();
            }
        }
    }

    /**
     * @param CopyPool $command
     * @return void
     * @throws InvalidUuidException
     */
    public function handleCopyPool(CopyPool $command)
    {
        $pool = $this->poolRepository->findById($command->getId());
        if (null === $pool) {
            return;
        }

        $copiedPool = $pool->copy($this->poolRepository->nextIdentity());
        $this->poolRepository->add($copiedPool);
        $copiedPool->publishEvents();
    }

    /**
     * @param RemovePool $command
     * @return void
     */
    public function handleRemovePool(RemovePool $command)
    {
        $pool = $this->poolRepository->findById($command->getId());

        if (null !== $pool) {
            $this->poolRepository->remove($pool);
            DomainEventPublisher::instance()->publish(
                new PoolRemoved(
                    $pool->getId()
                )
            );
        }
    }

    /**
     * @param RemovePoolItem $command
     * @return void
     * @throws InvalidUuidException
     */
    public function handleRemovePoolItem(RemovePoolItem $command)
    {
        $pool = $this->poolRepository->findById($command->getPoolId());

        if (null !== $pool) {
            $pool->removeItem(
                new AptoUuid($command->getPoolItemId())
            );

            $this->poolRepository->update($pool);
            $pool->publishEvents();
        }
    }

    /**
     * @param UpdatePool $command
     * @return void
     */
    public function handleUpdatePool(UpdatePool $command)
    {
        $pool = $this->poolRepository->findById($command->getId());

        if (null !== $pool) {
            $pool
                ->setName($this->getTranslatedValue($command->getName()));

            $this->poolRepository->update($pool);
            $pool->publishEvents();
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddPool::class => [
            'method' => 'handleAddPool',
            'bus' => 'command_bus',
            'aptoMessageName' => 'AddMaterialPickerPool'
        ];

        yield AddPoolItem::class => [
            'method' => 'handleAddPoolItem',
            'bus' => 'command_bus',
            'aptoMessageName' => 'AddMaterialPickerPoolItem'
        ];

        yield CopyPool::class => [
            'method' => 'handleCopyPool',
            'bus' => 'command_bus',
            'aptoMessageName' => 'CopyMaterialPickerPool'
        ];

        yield RemovePool::class => [
            'method' => 'handleRemovePool',
            'bus' => 'command_bus',
            'aptoMessageName' => 'RemoveMaterialPickerPool'
        ];

        yield RemovePoolItem::class => [
            'method' => 'handleRemovePoolItem',
            'bus' => 'command_bus',
            'aptoMessageName' => 'RemoveMaterialPickerPoolItem'
        ];

        yield UpdatePool::class => [
            'method' => 'handleUpdatePool',
            'bus' => 'command_bus',
            'aptoMessageName' => 'UpdateMaterialPickerPool'
        ];
    }
}