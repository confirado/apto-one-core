<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType;

class SelectBoxDataType extends DefaultDataType
{
    const IMPORT_COMMAND = 'ImportExportImportSelectBoxDataType';
    const DATA_TYPE = 'select-box';

    /**
     * @return array
     */
    public function getRequiredFields(): array
    {
        $fields = parent::getRequiredFields();
        return array_merge($fields, [
            'option-name'
        ]);
    }

    /**
     * @return array
     */
    public function getOptionalFields(): array
    {
        $fields = parent::getOptionalFields();
        return array_merge($fields, [
            'option-price_'
        ]);
    }
}