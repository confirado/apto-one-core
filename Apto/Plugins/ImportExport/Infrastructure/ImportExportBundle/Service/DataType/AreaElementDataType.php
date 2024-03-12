<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType;

class AreaElementDataType extends DefaultDataType
{
    const IMPORT_COMMAND = 'ImportExportImportAreaElementDataType';
    const DATA_TYPE = 'area-element';

    /**
     * @return array
     */
    public function getRequiredFields(): array
    {
        $fields = parent::getRequiredFields();
        return array_merge($fields, [
            'min_',
            'max_',
            'step_'
        ]);
    }

    /**
     * @return array
     */
    public function getOptionalFields(): array
    {
        $fields = parent::getOptionalFields();
        return array_merge($fields, [
            'prefix_',
            'suffix_',
            'price-matrix',
            'row-formula',
            'column-formula'
        ]);
    }
}