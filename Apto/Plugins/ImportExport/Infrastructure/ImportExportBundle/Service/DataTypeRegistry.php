<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service;

use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\DataType\DataType;

class DataTypeRegistry
{
    /**
     * @var array
     */
    private $dataTypes;

    /**
     * RegisteredDataTypes constructor.
     */
    public function __construct()
    {
        $this->dataTypes = [];
    }

    /**
     * @param DataType $dataType
     */
    public function addDataType(DataType $dataType)
    {
        $this->dataTypes[$dataType->getDataType()] = $dataType;
    }

    /**
     * @param string $dataType
     * @return DataType
     * @throws DataTypeNotRegisteredException
     */
    public function getDataType(string $dataType): DataType
    {
        if (!array_key_exists($dataType, $this->dataTypes)) {
            throw new DataTypeNotRegisteredException('Datatype "' . $dataType . '" not found.');
        }

        return $this->dataTypes[$dataType];
    }

    /**
     * @return array
     */
    public function getDataTypes(): array
    {
        return $this->dataTypes;
    }
}