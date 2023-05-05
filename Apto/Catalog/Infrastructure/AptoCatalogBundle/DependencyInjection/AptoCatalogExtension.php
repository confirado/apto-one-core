<?php
namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

use Apto\Catalog\Application\Backend\Service\Price\PriceExportProvider;
use Apto\Catalog\Application\Backend\Service\Price\PriceImportProvider;
use Apto\Catalog\Application\Backend\Service\Product\ProductPluginCopyProvider;
use Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry\RegisteredElementDefinition;
use Apto\Catalog\Application\Core\Service\ElementDefinitionRegistry\ElementStaticValuesProvider;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceCalculator;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider\PriceProvider;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider\ProductPriceProvider;
use Apto\Catalog\Application\Core\Service\PriceCalculator\PriceProvider\ProductSurchargeProvider;
use Apto\Catalog\Application\Core\Service\RenderImage\RenderImageProvider;
use Apto\Catalog\Application\Core\Service\RenderImage\RenderImageReducer;
use Apto\Catalog\Application\Frontend\Service\BasketItemDataProvider;

class AptoCatalogExtension extends Extension
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

        $container->registerForAutoconfiguration(PriceCalculator::class)->addTag('register_price_calculator');
        $container->registerForAutoconfiguration(PriceProvider::class)->addTag('register_price_provider');
        $container->registerForAutoconfiguration(ProductPriceProvider::class)->addTag('register_product_price_provider');
        $container->registerForAutoconfiguration(PriceImportProvider::class)->addTag('register_price_import_provider');
        $container->registerForAutoconfiguration(PriceExportProvider::class)->addTag('register_price_export_provider');
        $container->registerForAutoconfiguration(ProductSurchargeProvider::class)->addTag('register_product_surcharge_provider');
        $container->registerForAutoconfiguration(RenderImageProvider::class)->addTag('register_render_image_provider');
        $container->registerForAutoconfiguration(RenderImageReducer::class)->addTag('register_render_image_reducer');
        $container->registerForAutoconfiguration(RegisteredElementDefinition::class)->addTag('register_element_definition');
        $container->registerForAutoconfiguration(ElementStaticValuesProvider::class)->addTag('register_element_static_values_provider');
        $container->registerForAutoconfiguration(ProductPluginCopyProvider::class)->addTag('register_product_plugin_copy_provider');
        $container->registerForAutoconfiguration(BasketItemDataProvider::class)->addTag('register_basket_item_data_provider');
    }
}
