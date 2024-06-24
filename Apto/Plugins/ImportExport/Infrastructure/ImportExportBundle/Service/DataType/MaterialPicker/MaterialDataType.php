<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\MaterialPicker;

use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\AbstractDataType;

class MaterialDataType extends AbstractDataType
{
    const IMPORT_COMMAND = 'ImportExportImportMaterialDataType';
    const DATA_TYPE = 'material';
    const REQUIRED_FIELDS = [
        'identifier',
        'name',
        'preisgruppe',
        'pool'
    ];
    const OPTIONAL_FIELDS = [
        'beschreibung',
        'reflexion',
        'transmission',
        'absorption',
        'vorschau-bild',
        'condition_',
    ];
}
