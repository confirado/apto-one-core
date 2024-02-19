<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType;

class PricePerUnitDataType extends DefaultDataType
{
    const IMPORT_COMMAND = 'ImportExportImportPricePerUnitDataType';
    const DATA_TYPE = 'price-per-unit';

    /**
     * @return array
     */
    public function getRequiredFields(): array
    {
        $fields = parent::getRequiredFields();
        return array_merge($fields, [
            'reference-value_',
            'definition-price_',
            'conversion-factor'
        ]);
    }
}