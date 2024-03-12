<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType;

class DefaultDataType extends AbstractDataType
{
    const IMPORT_COMMAND = 'ImportExportImportDefaultDataType';
    const DATA_TYPE = 'default';
    const REQUIRED_FIELDS = [
        'product-identifier',
        'section-identifier',
        'element-identifier'
    ];

    const OPTIONAL_FIELDS = [
        'product-name',
        'product-beschreibung',
        'product-step-by-step',
        'product-url',
        'product-preview-image',
        'product-price_',
        'product-discount_',
        'section-name',
        'section-beschreibung',
        'section-position',
        'section-required',
        'section-price_',
        'section-discount_',
        'element-name',
        'element-beschreibung',
        'element-position',
        'element-default',
        'element-preview-image',
        'element-price_',
        'element-discount_',
        'element-render-image_',
        'extended-price-calculation-active',
        'extended-price-calculation-formula'
    ];
}
