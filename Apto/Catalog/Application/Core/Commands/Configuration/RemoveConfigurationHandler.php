<?php

namespace Apto\Catalog\Application\Core\Commands\Configuration;

use Apto\Base\Application\Core\CommandHandlerInterface;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventPublisher;
use Apto\Catalog\Domain\Core\Model\Configuration\BasketConfigurationRemoved;
use Apto\Catalog\Domain\Core\Model\Configuration\BasketConfigurationRepository;

class RemoveConfigurationHandler implements CommandHandlerInterface
{
    /**
     * @var BasketConfigurationRepository
     */
    protected $basketConfigurationRepository;

    /**
     * @param BasketConfigurationRepository $basketConfigurationRepository
     */
    public function __construct(BasketConfigurationRepository $basketConfigurationRepository)
    {
        $this->basketConfigurationRepository = $basketConfigurationRepository;
    }

    /**
     * Handle RemoveBasketConfiguration commands
     * @param RemoveBasketConfiguration $command
     */
    public function handleRemoveBasketConfiguration(RemoveBasketConfiguration $command)
    {
        $configuration = $this->basketConfigurationRepository->findById($command->getId());

        if (null !== $configuration) {
            $this->basketConfigurationRepository->remove($configuration);
            DomainEventPublisher::instance()->publish(
                new BasketConfigurationRemoved(
                    $configuration->getId()
                )
            );
        }
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield RemoveBasketConfiguration::class => [
            'method' => 'handleRemoveBasketConfiguration',
            'bus' => 'command_bus'
        ];
    }
}