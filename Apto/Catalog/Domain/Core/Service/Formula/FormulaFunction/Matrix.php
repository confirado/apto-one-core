<?php

namespace Apto\Catalog\Domain\Core\Service\Formula\FormulaFunction;

use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Service\Formula\Exception\FunctionParserException;

class Matrix extends AbstractFormulaFunction
{

    /**
     * @var MediaFileSystemConnector
     */
    protected $fileSystemConnector;

    /**
     * @param MediaFileSystemConnector $fileSystemConnector
     */
    public function __construct(MediaFileSystemConnector $fileSystemConnector)
    {
        $this->fileSystemConnector = $fileSystemConnector;
    }

    /**
     * @inheritDoc
     */
    public function parse(array $params, array $variables = [], array $aliases = [], ?State $state = null): string
    {
        // assert valid param count
        if (count($params) != 3) {
            throw new FunctionParserException(sprintf(
                'Function "%s" expected exactly 3 parameters, but %s given.',
                self::getName(),
                count($params)
            ));
        }

        // get params
        $filePath = $params[0];
        $row = self::replaceParam($params[1], $variables);
        $column = self::replaceParam($params[2], $variables);

        // return value from matrix
        return self::getValueFromMatrix(
            $this->fileSystemConnector->getAbsolutePath($filePath),
            $row,
            $column
        );
    }

    /**
     * @param string $filePath
     * @param string $rowSearchValue
     * @param string $columnSearchValue
     * @return string
     */
    private static function getValueFromMatrix(string $filePath, string $rowSearchValue, string $columnSearchValue): string
    {
        $cells = self::getMatrixCells(file_get_contents($filePath));
        $tree = self::getMatrixTree($cells);

        foreach ($tree as $rowValue => $row) {
            if ($rowValue < $rowSearchValue) {
                continue;
            }
            foreach ($row as $columnValue => $value) {
                if (
                    $columnValue < $columnSearchValue ||
                    trim('' . $value) === ''
                ) {
                    continue;
                }
                return $value;
            }
        }

        return '0';
    }

    /**
     * @todo implement utf8 detect from AbstractCsvImport
     * @param string $content
     * @return array
     */
    private static function getMatrixCells(string $content): array
    {
        // set used variables
        $cells = [];
        $colIndex = null;

        // create file handle in memory
        $fileHandle = fopen("php://memory", 'r+');
        fputs($fileHandle, $content);
        rewind($fileHandle);

        // read every line
        while($line = fgets($fileHandle)) {
            if (null === $colIndex) {
                // first line in csv
                $colIndex = str_getcsv($line, ';', '"', ',');
            } else {
                // data lines in csv
                $fields = str_getcsv($line, ';', '"', ',');
                $fields = @array_combine($colIndex, $fields);
                self::parseMatrixLine($fields, $cells);
            }
        }

        // close file handle in memory
        fclose($fileHandle);

        // return cells
        return $cells;
    }

    /**
     * @param array $fields
     * @param array $cells
     */
    private static function parseMatrixLine(array $fields, array &$cells)
    {
        $row = null;
        foreach ($fields as $column => $value) {
            if ($row === null) {
                $row = $value;
                continue;
            }
            $cells[] = [
                'column' => (string) $column,
                'row' => (string) $row,
                'value' => (string) $value
            ];
        }
    }

    /**
     * @param array $cells
     * @return array
     */
    private static function getMatrixTree(array $cells): array
    {
        $tree = [];

        foreach ($cells as $cell) {
            $tree[$cell['row']][$cell['column']] = $cell['value'];
        }

        foreach ($tree as &$row) {
            ksort($row);
        }
        ksort($tree);

        return $tree;
    }

}
