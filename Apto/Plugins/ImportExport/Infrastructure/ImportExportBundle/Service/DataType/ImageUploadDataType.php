<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType;

class ImageUploadDataType extends DefaultDataType
{
    const IMPORT_COMMAND = 'ImportExportImportImageUploadDataType';
    const DATA_TYPE = 'image-upload';

    /**
     * @return array
     */
    public function getRequiredFields(): array
    {
        $fields = parent::getRequiredFields();
        return array_merge($fields, [
            'canvas'
        ]);
    }
}
