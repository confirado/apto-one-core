<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus;

use Throwable;

use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

use Apto\Base\Application\Core\QueryInterface;

class QueryBus
{
    use HandleTrait {
        handle as protected traitHandle;
    }

    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    /**
     * @see https://symfony.com/doc/5.4/messenger/multiple_buses.html
     * name $queryBus is important because symfony will autowire the correct bus by naming conventions
     *
     * @param MessageBusInterface $queryBus
     */
    public function __construct(MessageBusInterface $queryBus)
    {
        $this->messageBus = $queryBus;
    }

    /**
     * @param QueryInterface $message
     * @param array|null $return
     * @return void
     * @throws Throwable
     */
    public function handle(QueryInterface $message, ?array &$return)
    {
        try {
            $return = $this->traitHandle($message);
        } catch (HandlerFailedException $exception) {
            foreach ($exception->getNestedExceptions() as $nestedException) {
                throw $nestedException;
            };
        }
    }
}
