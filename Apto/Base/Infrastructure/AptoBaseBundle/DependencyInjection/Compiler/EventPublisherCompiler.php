<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class EventPublisherCompiler implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has('Apto\Base\Infrastructure\AptoBaseBundle\DomainEvent\DomainEventSubscriberFactory')) {
            return;
        }

        $definition = $container->findDefinition('Apto\Base\Infrastructure\AptoBaseBundle\DomainEvent\DomainEventSubscriberFactory');
        $definition->setPublic(true);

        // find all service IDs with the domain_event_subscriber tag
        $taggedServices = $container->findTaggedServiceIds('domain_event_subscriber');

        foreach ($taggedServices as $id => $tags) {
            // add the subscriber to the generic publisher service
            $definition->addMethodCall('addSubscriber', [
                    new Reference($id)
                ]
            );
        }
    }
}