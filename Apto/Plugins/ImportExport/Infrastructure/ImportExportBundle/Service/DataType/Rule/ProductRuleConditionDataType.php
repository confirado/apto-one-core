<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\Rule;

use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\AbstractDataType;

class ProductRuleConditionDataType extends AbstractDataType
{
    const PRE_IMPORT_COMMAND = 'ImportExportPreImportRuleConditionDataType';
    const IMPORT_COMMAND = 'ImportExportImportRuleConditionDataType';
    const DATA_TYPE = 'product-rule-condition';
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
