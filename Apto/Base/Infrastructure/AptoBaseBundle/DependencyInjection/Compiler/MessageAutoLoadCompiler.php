<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\DependencyInjection\Compiler;

use ReflectionException;
use Apto\Base\Application\Core\MessageHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;

class MessageAutoLoadCompiler implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     * @throws ReflectionException
     */
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has('Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\MessageBusManager')) {
            return;
        }

        $definitionMessageBusManager = $container->findDefinition('Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\MessageBusManager');

        // register command handlers
        $this->registerCommands($container, $definitionMessageBusManager);

        // register query handlers
        $this->registerQueries($container, $definitionMessageBusManager);
    }

    /**
     * @param ContainerBuilder $container
     * @param Definition $definitionMessageBusManager
     * @throws ReflectionException
     */
    private function registerCommands(ContainerBuilder $container, Definition $definitionMessageBusManager)
    {
        // add command handlers
        $commandHandlers = $this->getMapping($container, 'command_handler_autoload');
        $definitionMessageBusManager->addMethodCall('addCommands', [$commandHandlers]);
    }

    /**
     * @param ContainerBuilder $container
     * @param Definition $definitionMessageBusManager
     * @throws ReflectionException
     */
    private function registerQueries(ContainerBuilder $container, Definition $definitionMessageBusManager)
    {
        // add query handlers
        $queryHandlers = $this->getMapping($container, 'query_handler_autoload');
        $definitionMessageBusManager->addMethodCall('addQueries', [$queryHandlers]);
    }

    /**
     * @param ContainerBuilder $container
     * @param string $tag
     * @return array
     * @throws ReflectionException
     */
    private function getMapping(ContainerBuilder $container, string $tag): array
    {
        $taggedHandler = $container->findTaggedServiceIds($tag);
        $mapping = [];

        foreach ($taggedHandler as $id => $tags) {
            $handlerClass = $container->findDefinition($id)->getClass();
            $handlerReflection = $container->getReflectionClass($handlerClass);

            if (!$handlerReflection->implementsInterface(MessageHandlerInterface::class)) {
                continue;
            }

            /** @var MessageHandlerInterface $handlerReflectionClass */
            $handlerReflectionClass = $handlerReflection->getName();

            foreach ($handlerReflectionClass::getHandledMessages() as $message => $options) {
                if (!array_key_exists('bus', $options)) {
                    throw new \InvalidArgumentException('Registered messages must define a specific bus. Add the "bus" option and define "query_bus", "command_bus" or "event_bus". Message: ' . $message);
                }

                if ($options['bus'] !== 'query_bus' && $options['bus'] !== 'command_bus' && $options['bus'] !== 'event_bus') {
                    throw new \InvalidArgumentException('Messages can only be registered for these busses: "query_bus", "command_bus" or "event_bus". Message: ' . $message);
                }

                $messageReflection = $container->getReflectionClass($message);
                $messageName = $messageReflection->getShortName();

                if (array_key_exists('aptoMessageName', $options)) {
                    $messageName = $options['aptoMessageName'];
                }

                if (array_key_exists('aptoMessagePrefix', $options)) {
                    $messageName = $options['aptoMessagePrefix'] . $messageName;
                }

                $mapping[$messageName] = $message;
            }
        }

        return $mapping;
    }
}