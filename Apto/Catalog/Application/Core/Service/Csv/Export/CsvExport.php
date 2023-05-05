<?php

namespace Apto\Catalog\Application\Core\Service\Csv\Export;

class CsvExport
{
    /**
     * @var array
     */
    private $rows;

    /**
     * @var string
     */
    private $delimiter;

    /**
     * @var string
     */
    private $csvSting;

    /**
     * @var array
     */
    private $sortOrderRowEntries;

    /**
     * CsvExport constructor.
     * @param array $rows
     * @param string $delimiter
     */
    public function __construct(array $rows, string $delimiter)
    {
        $this->rows = $rows;
        $this->delimiter = $delimiter;
        $this->csvSting = '';

        $this->initSortOrderRowEntries();
    }

    /**
     * @return string
     */
    public function getCSVString(): string
    {
        if (count($this->rows) < 1) {
            return $this->csvSting;
        }

        $csv = fopen('php://temp/maxmemory:'. (5*1024*1024), 'r+');

        foreach ($this->rows as $row) {
            fputcsv($csv, $this->getSortedRow($row), $this->delimiter);
        }

        rewind($csv);

        $this->csvSting .= stream_get_contents($csv);

        // add BOM
        $this->addBom();

        // put it all in a variable
        return $this->csvSting;
    }

    /**
     * @param array $entries
     */
    public function createHeader(array $entries)
    {
        $this->csvSting = implode($this->delimiter, array_keys($entries)) . chr(10) . $this->csvSting;
    }

    /**
     * @return void
     */
    private function addBom()
    {
        $csv = fopen('php://temp/maxmemory:'. (5*1024*1024), 'r+');
        fputs($csv, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF)));
        rewind($csv);

        // put it all in a variable
        $this->csvSting = stream_get_contents($csv) . $this->csvSting;
    }

    /**
     * @param array $row
     * @return array
     */
    private function getSortedRow(array $row): array
    {
        $sortedRow = [];

        // iterate all row entries
        foreach ($this->sortOrderRowEntries as $index => $rowEntryKey) {
            // add entry to line if entry is set in row entries
            if (array_key_exists($rowEntryKey, $row)) {
                $sortedRow[] = $this->writeRowEntry($row[$rowEntryKey]);
            } else {
                $sortedRow[] = '';
            }
        }

        return $sortedRow;
    }

    /**
     * @return void
     */
    private function initSortOrderRowEntries()
    {
        if (count($this->rows) < 1) {
            $this->sortOrderRowEntries = [];
            return;
        }
        $this->sortOrderRowEntries = array_keys($this->rows[0]);
    }

    /**
     * @param $entry
     * @return string
     */
    private function writeRowEntry($entry): string
    {
        if (is_array($entry)) {
            $entry = implode(' | ', $entry);
        }
        if ($entry === true) {
            $entry = 'true';
        }
        if ($entry === false) {
            $entry = 'false';
        }
        if (is_null($entry)) {
            $entry = 'null';
        }
        return strval($entry);
    }
}
