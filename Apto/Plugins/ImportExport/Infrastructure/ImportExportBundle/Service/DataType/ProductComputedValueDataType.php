<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType;

class ProductComputedValueDataType extends AbstractDataType
{
    const IMPORT_COMMAND = 'ImportExportImportProductComputedValueDataType';
    const DATA_TYPE = 'product-computed-value';
    const REQUIRED_FIELDS = [
        'product-identifier',
        'name',
        'formula'
    ];

    const OPTIONAL_FIELDS = [
        'section-identifier',
        'element-identifier',
        'property',
        'alias'
    ];
}