<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\MaterialPicker;

use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\AbstractDataType;

class MaterialPriceGroupDataType extends AbstractDataType
{
    const IMPORT_COMMAND = 'ImportExportImportMaterialPriceGroupDataType';
    const DATA_TYPE = 'material-pricegroup';
    const REQUIRED_FIELDS = [
        'name'
    ];
    const OPTIONAL_FIELDS = [
        'public-name',
        'surcharge',
        'price-matrix',
        'row-formula',
        'column-formula'
    ];
}
