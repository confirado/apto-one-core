<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType;

class FloatInputDataType extends DefaultDataType
{
    const IMPORT_COMMAND = 'ImportExportImportFloatInputDataType';
    const DATA_TYPE = 'float-input';

    /**
     * @return array
     */
    public function getRequiredFields(): array
    {
        $fields = parent::getRequiredFields();
        return array_merge($fields, [
            'min-length',
            'max-length',
            'step-length'
        ]);
    }

    /**
     * @return array
     */
    public function getOptionalFields(): array
    {
        $fields = parent::getOptionalFields();
        return array_merge($fields, [
            'prefix-length',
            'suffix-length',
            'conversion-factor',
            'reference-value_'
        ]);
    }
}