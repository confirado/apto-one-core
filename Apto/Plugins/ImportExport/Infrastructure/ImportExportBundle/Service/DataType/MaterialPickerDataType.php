<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType;

class MaterialPickerDataType extends DefaultDataType
{
    const IMPORT_COMMAND = 'ImportExportImportMaterialPickerDataType';
    const DATA_TYPE = 'material-picker';

    /**
     * @return array
     */
    public function getRequiredFields(): array
    {
        $fields = parent::getRequiredFields();
        return array_merge($fields, [
            'pool'
        ]);
    }
}