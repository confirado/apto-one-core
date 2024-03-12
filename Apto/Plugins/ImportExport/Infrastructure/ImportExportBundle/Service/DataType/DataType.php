<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType;

interface DataType
{
    /**
     * @return string
     */
    public function getDataType(): string;

    /**
     * @return array
     */
    public function getRequiredFields(): array;

    /**
     * @return array
     */
    public function getOptionalFields(): array;

    /**
     * @return string
     */
    public function getImportCommand(): string;

    /**
     * @return string|null
     */
    public function getPreImportCommand(): ?string;
}