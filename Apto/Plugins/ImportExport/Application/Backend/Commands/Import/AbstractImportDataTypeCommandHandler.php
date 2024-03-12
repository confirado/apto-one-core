<?php

namespace Apto\Plugins\ImportExport\Application\Backend\Commands\Import;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;

abstract class AbstractImportDataTypeCommandHandler extends AbstractCommandHandler
{
    /**
     * @param array $fields
     * @param string $field
     * @param string|null $separator
     * @return array
     */
    protected function getMultipleFieldValues(array $fields, string $field, ?string $separator = null): array
    {
        $values = [];
        foreach ($fields as $key => $value) {
            // multiple fields ends with
            if (!$this->isMultipleFieldForKey($field, $key)) {
                continue;
            }

            $values[$key] = $separator ? explode($separator, $value) : $value;
        }
        return $values;
    }

    /**
     * @todo refactor multiple implementation in Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\Import
     * @param string $field
     * @param string $key
     * @return bool
     */
    protected function isMultipleFieldForKey(string $field, string $key): bool
    {
        $keyPos = strpos($key, $field);
        $keySeparators = substr_count($key, '_');
        $fieldSeparators = substr_count($field, '_');

        if ($keyPos === 0 && $keySeparators === 1 && $fieldSeparators === 1) {
            return true;
        }

        return false;
    }
}