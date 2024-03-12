<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service;

use Apto\Base\Application\Backend\Service\AbstractCsvImport;

class Import extends AbstractCsvImport
{
    /**
     * @var DataTypeRegistry
     */
    private $dataTypeRegistry;

    /**
     * @var array
     */
    private $validLines;

    /**
     * @var array
     */
    private $lastLine;

    /**
     * Import constructor.
     * @param DataTypeRegistry $dataTypeRegistry
     */
    public function __construct(DataTypeRegistry $dataTypeRegistry)
    {
        parent::__construct();
        $this->dataTypeRegistry = $dataTypeRegistry;
        $this->validLines = [];
        $this->lastLine = [];
    }

    /**
     * @todo this is an encoding fix for a bug in our abstract import class, remove this method when bug is fixed
     * @return array|bool
     */
    protected function readLine()
    {
        $this->lineNumber++;
        $line = fgets($this->fileHandle);

        // read failed or EOF
        if (false === $line) {
            return false;
        }

        // detect UTF-8 encoding, encode otherwise
        if (!self::detectUTF8($line)) {
            $line = utf8_encode($line);
        }

        return str_getcsv($line, self::DELIMITER, self::ENCLOSURE, self::ESCAPE_CHAR);
    }

    /**
     * @return array
     */
    public function getValidLines(): array
    {
        return $this->validLines;
    }

    /**
     * @param string $dataType
     * @return array
     */
    public function getValidLinesByDataType(string $dataType): array
    {
        $validLines = [];

        foreach ($this->getValidLines() as $validLine) {
            if ($validLine['data-type'] !== $dataType) {
                continue;
            }

            $validLines[] = $validLine;
        }

        return $validLines;
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    /**
     * @param string $dataType
     * @return bool
     */
    public function hasDataType(string $dataType): bool
    {
        foreach ($this->getValidLines() as $validLine) {
            if ($validLine['data-type'] !== $dataType) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $fields
     * @throws DataTypeNotRegisteredException
     * @throws DataTypeRequiredFieldException
     */
    protected function parseLine(array $fields)
    {
        //$fields = $this->fillRedundantData($fields);
        $dataType = $this->dataTypeRegistry->getDataType($fields['data-type']);

        foreach ($dataType->getRequiredFields() as $requiredField) {
            if ($this->isMultipleField($requiredField)) {
                $this->assertValidMultipleField($requiredField, $fields);
            } else {
                $this->assertValidSimpleField($requiredField, $fields);
            }
        }

        $this->validLines[] = $fields;
    }

    /**
     * @param string $requiredField
     * @param array $fields
     * @throws DataTypeRequiredFieldException
     */
    protected function assertValidSimpleField(string $requiredField, array $fields)
    {
        // required field not found
        if (!array_key_exists($requiredField, $fields)) {
            throw new DataTypeRequiredFieldException('Required field "' . $requiredField . '" was not found.');
        }

        // required field is empty
        if (trim($fields[$requiredField] === '')) {
            throw new DataTypeRequiredFieldException('Required field "' . $requiredField . '" was empty.');
        }
    }

    /**
     * @param string $requiredField
     * @param array $fields
     * @throws DataTypeRequiredFieldException
     */
    protected function assertValidMultipleField(string $requiredField, array $fields)
    {
        $firstMultipleField = $this->getFirstMultipleField($requiredField, $fields);

        if (null === $firstMultipleField) {
            throw new DataTypeRequiredFieldException('Required field "' . $requiredField . '" was not found.');
        }

        // required field is empty
        if (trim($fields[$firstMultipleField] === '')) {
            throw new DataTypeRequiredFieldException('Required field "' . $firstMultipleField . '" was empty.');
        }
    }

    /**
     * @param string $field
     * @return bool
     */
    protected function isMultipleField(string $field): bool
    {
        return substr($field, -1) === '_';
    }

    /**
     * @param string $field
     * @param array $fields
     * @return string|null
     */
    protected function getFirstMultipleField(string $field, array $fields): ?string
    {
        foreach (array_keys($fields) as $key) {
            if ($this->isMultipleFieldForKey($field, $key)) {
                return $key;
            }
        }

        return null;
    }

    /**
     * @todo refactor multiple implementation in Apto\Plugins\ImportExport\Application\Backend\Commands\Import\AbstractImportDataTypeCommandHandler
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

    /**
     * @param array $fields
     * @return array
     */
    protected function fillRedundantData(array $fields): array
    {
        if (empty($this->lastLine)) {
            $this->lastLine = $fields;
            return $fields;
        }

        foreach ($fields as $key => $field) {
            if (!$fields[$key]) {
                $fields[$key] = $this->lastLine[$key];
            }
        }

        $this->lastLine = $fields;
        return $fields;
    }
}