<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\Rule;

use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\AbstractDataType;

class ProductRuleImplicationDataType extends AbstractDataType
{
    const PRE_IMPORT_COMMAND = 'ImportExportPreImportRuleImplicationDataType';
    const IMPORT_COMMAND = 'ImportExportImportRuleImplicationDataType';
    const DATA_TYPE = 'product-rule-implication';
    const REQUIRED_FIELDS = [
        'product-identifier',
        'name',
        'section-identifier',
        'operator'
    ];
    const OPTIONAL_FIELDS = [
        'element-identifier',
        'property',
        'value'
    ];
}
