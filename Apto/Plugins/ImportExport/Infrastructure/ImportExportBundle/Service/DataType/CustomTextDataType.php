<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType;

class CustomTextDataType extends DefaultDataType
{
    const IMPORT_COMMAND = 'ImportExportImportCustomTextDataType';
    const DATA_TYPE = 'custom-text';

    /**
     * @return array
     */
    public function getRequiredFields(): array
    {
        $fields = parent::getRequiredFields();
        return array_merge($fields, [
            'min-length',
            'max-length'
        ]);
    }

    /**
     * @return array
     */
    public function getOptionalFields(): array
    {
        $fields = parent::getOptionalFields();
        return array_merge($fields, [
            'rendering'
        ]);
    }
}