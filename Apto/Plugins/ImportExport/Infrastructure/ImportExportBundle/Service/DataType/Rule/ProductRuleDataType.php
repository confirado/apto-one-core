<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\Rule;

use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\AbstractDataType;

class ProductRuleDataType extends AbstractDataType
{
    const IMPORT_COMMAND = 'ImportExportImportRuleDataType';
    const DATA_TYPE = 'product-rule';
    const REQUIRED_FIELDS = [
        'product-identifier',
        'name',
        'conditions-operator',
        'implications-operator'
    ];
    const OPTIONAL_FIELDS = [
        'active',
        'soft-rule',
        'error-message'
    ];
}
