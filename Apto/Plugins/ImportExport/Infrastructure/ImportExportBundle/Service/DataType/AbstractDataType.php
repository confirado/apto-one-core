<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType;

abstract class AbstractDataType implements DataType
{
    const PRE_IMPORT_COMMAND = null;
    const IMPORT_COMMAND = '';
    const DATA_TYPE = 'data-type';
    const REQUIRED_FIELDS = [];
    const OPTIONAL_FIELDS = [];

    /**
     * @return string
     */
    public function getDataType(): string
    {
        return static::DATA_TYPE;
    }

    /**
     * @return array
     */
    public function getRequiredFields(): array
    {
        return static::REQUIRED_FIELDS;
    }

    /**
     * @return array
     */
    public function getOptionalFields(): array
    {
        return static::OPTIONAL_FIELDS;
    }

    /**
     * @return string
     */
    public function getImportCommand(): string
    {
        return static::IMPORT_COMMAND;
    }

    /**
     * @return string|null
     */
    public function getPreImportCommand(): ?string
    {
        return static::PRE_IMPORT_COMMAND;
    }
}