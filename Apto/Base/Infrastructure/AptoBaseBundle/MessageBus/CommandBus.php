<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus;

use Throwable;

use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

use Apto\Base\Application\Core\CommandInterface;

class CommandBus
{
    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    /**
     * @see https://symfony.com/doc/5.4/messenger/multiple_buses.html
     * name $commandBus is important because symfony will autowire the correct bus by naming conventions
     *
     * @param MessageBusInterface $commandBus
     */
    public function __construct(MessageBusInterface $commandBus)
    {
        $this->messageBus = $commandBus;
    }

    /**
     * @param CommandInterface $message
     * @return void
     * @throws Throwable
     */
    public function handle(CommandInterface $message)
    {
        try {
            $this->messageBus->dispatch($message);
        } catch (HandlerFailedException $exception) {
            foreach ($exception->getNestedExceptions() as $nestedException) {
                throw $nestedException;
            };
        }

    }
}