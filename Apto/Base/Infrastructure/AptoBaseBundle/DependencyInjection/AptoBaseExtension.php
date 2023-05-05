<?php
namespace Apto\Base\Infrastructure\AptoBaseBundle\DependencyInjection;

use Apto\Base\Application\Core\CommandHandlerInterface;
use Apto\Base\Application\Core\QueryHandlerInterface;
use Apto\Base\Application\Core\Service\ContentSnippet\ContentSnippetProvider;
use Apto\Base\Application\Core\Service\Translation\TranslationExportProvider;
use Apto\Base\Application\Core\Service\Translation\TranslationImportProvider;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventSubscriber;
use Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\CommandBusFirewallRule;
use Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\QueryBusFirewallRule;
use Apto\Base\Infrastructure\AptoBaseBundle\Template\BackendTemplateInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class AptoBaseExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @return void
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__).'/Resources/config'));
        $loader->load('services.yaml');

        $container->registerForAutoconfiguration(CommandHandlerInterface::class)->addTag('command_handler_autoload');
        $container->registerForAutoconfiguration(QueryHandlerInterface::class)->addTag('query_handler_autoload');
        $container->registerForAutoconfiguration(BackendTemplateInterface::class)->addTag('register_backend_template');
        $container->registerForAutoconfiguration(CommandBusFirewallRule::class)->addTag('message_bus_firewall_command_rule');
        $container->registerForAutoconfiguration(QueryBusFirewallRule::class)->addTag('message_bus_firewall_query_rule');
        $container->registerForAutoconfiguration(DomainEventSubscriber::class)->addTag('domain_event_subscriber');
        $container->registerForAutoconfiguration(ContentSnippetProvider::class)->addTag('register_content_snippet_provider');
        $container->registerForAutoconfiguration(TranslationExportProvider::class)->addTag('register_translation_export_provider');
        $container->registerForAutoconfiguration(TranslationImportProvider::class)->addTag('register_translation_import_provider');
    }
}