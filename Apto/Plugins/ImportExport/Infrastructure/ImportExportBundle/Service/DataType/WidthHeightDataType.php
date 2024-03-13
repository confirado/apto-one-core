<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType;

class WidthHeightDataType extends DefaultDataType
{
    const IMPORT_COMMAND = 'ImportExportImportWidthHeightDataType';
    const DATA_TYPE = 'width-height';

    /**
     * @return array
     */
    public function getRequiredFields(): array
    {
        $fields = parent::getRequiredFields();
        return array_merge($fields, [
            'min-width',
            'min-height',
            'max-width',
            'max-height',
            'step-width',
            'step-height'
        ]);
    }

    /**
     * @return array
     */
    public function getOptionalFields(): array
    {
        $fields = parent::getOptionalFields();
        return array_merge($fields, [
            'prefix-width',
            'prefix-height',
            'suffix-width',
            'suffix-height',
            'price-matrix'
        ]);
    }
}