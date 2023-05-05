<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class MessageBusFirewallCompiler implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has('Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\MessageBusFirewall')) {
            return;
        }

        $definition = $container->findDefinition('Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\MessageBusFirewall');

        // find all service IDs with the message_bus_firewall_command_rule tag
        $taggedCommandRuleServices = $container->findTaggedServiceIds('message_bus_firewall_command_rule');

        // find all service IDs with the message_bus_firewall_query_rule tag
        $taggedQueryRuleServices = $container->findTaggedServiceIds('message_bus_firewall_query_rule');

        foreach ($taggedCommandRuleServices as $id => $tags) {
            // add the firewall rule service to the generic firewall service
            foreach ($tags as $attributes) {
                $definition->addMethodCall('addCommandRule', [
                        new Reference($id),
                        $attributes['command']
                    ]
                );
            }
        }

        foreach ($taggedQueryRuleServices as $id => $tags) {
            // add the firewall rule service to the generic firewall service
            foreach ($tags as $attributes) {
                $definition->addMethodCall('addQueryRule', [
                        new Reference($id),
                        $attributes['query']
                    ]
                );
            }
        }
    }
}