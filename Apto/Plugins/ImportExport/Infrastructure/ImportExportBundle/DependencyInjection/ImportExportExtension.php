<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\DependencyInjection;

use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\DefaultDataType;
use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\AreaElementDataType;
use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\WidthHeightDataType;
use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\MaterialPickerDataType;
use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\CustomTextDataType;
use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\PricePerUnitDataType;
use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\FloatInputDataType;
use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\SelectBoxDataType;
use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\ProductComputedValueDataType;
use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\MaterialPicker\MaterialDataType;
use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\MaterialPicker\MaterialPriceGroupDataType;
use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\Rule\ProductRuleDataType;
use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\Rule\ProductRuleConditionDataType;
use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\Rule\ProductRuleImplicationDataType;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ImportExportExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__).'/Resources/config'));
        $loader->load('services.yaml');

        $container->registerForAutoconfiguration(DefaultDataType::class)->addTag('register_import_export_data_type');
        $container->registerForAutoconfiguration(AreaElementDataType::class)->addTag('register_import_export_data_type');
        $container->registerForAutoconfiguration(WidthHeightDataType::class)->addTag('register_import_export_data_type');
        $container->registerForAutoconfiguration(MaterialPickerDataType::class)->addTag('register_import_export_data_type');
        $container->registerForAutoconfiguration(CustomTextDataType::class)->addTag('register_import_export_data_type');
        $container->registerForAutoconfiguration(PricePerUnitDataType::class)->addTag('register_import_export_data_type');
        $container->registerForAutoconfiguration(FloatInputDataType::class)->addTag('register_import_export_data_type');
        $container->registerForAutoconfiguration(SelectBoxDataType::class)->addTag('register_import_export_data_type');
        $container->registerForAutoconfiguration(ProductComputedValueDataType::class)->addTag('register_import_export_data_type');
        $container->registerForAutoconfiguration(MaterialDataType::class)->addTag('register_import_export_data_type');
        $container->registerForAutoconfiguration(MaterialPriceGroupDataType::class)->addTag('register_import_export_data_type');
        $container->registerForAutoconfiguration(ProductRuleDataType::class)->addTag('register_import_export_data_type');
        $container->registerForAutoconfiguration(ProductRuleConditionDataType::class)->addTag('register_import_export_data_type');
        $container->registerForAutoconfiguration(ProductRuleImplicationDataType::class)->addTag('register_import_export_data_type');
    }
}