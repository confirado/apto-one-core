<?php

namespace Apto\Base\Application\Backend\Service;

use Apto\Base\Domain\Core\Model\FileSystem\File\File;

abstract class AbstractCsvImport
{
    /**
     * mandatory columns
     */
    protected array $mandatoryColumns = [];

    /**
     * several csv control chars
     */
    const BOM = "\xef\xbb\xbf";
    const DELIMITER = ';';
    const ENCLOSURE = '"';
    const ESCAPE_CHAR = ',';
    const NEWLINE = "\n";

    /**
     * @var resource
     */
    protected $fileHandle;

    /**
     * @var bool
     */
    protected $isUTF8;

    /**
     * @var int
     */
    protected $lineNumber;

    /**
     * @var array
     */
    protected $colIndex;

    /**
     * @var array
     */
    protected $errors;

    /**
     * AbstractImport constructor.
     */
    public function __construct()
    {
        $this->errors = [];
    }

    /**
     * return errors
     * @return array
     */
    public function getErrors(): array
    {
        $errors = $this->errors;
        $this->errors = [];
        return $errors;
    }

    /**
     * @param File $file
     * @TODO refactor to use FileSystemConnector
     */
    public function importCsvFile(File $file)
    {
        // reset column index
        $this->colIndex = [];

        // open file
        if (!file_exists($file->getPath())) {
            $this->errors[] = 'Die Datei konnte nicht gefunden werden.';
            return;
        }
        $this->fileHandle = fopen($file->getPath(), 'r');
        $this->lineNumber = 0;

        // check if EOF
        if (feof($this->fileHandle)) {
            fclose($this->fileHandle);
            $this->errors[] = 'Die hochgeladene Datei ist leer.';
            return;
        }

        // detect and skip BOM if present
        $this->isUTF8 = true;
        $buffer = fread($this->fileHandle, strlen(self::BOM));
        if ($buffer !== self::BOM) {
            $this->isUTF8 = false;
            rewind($this->fileHandle);
        }

        // read column index from first line
        $this->colIndex = $this->readLine();

        // check mandatory columns
        $missingColumns = [];
        foreach ($this->mandatoryColumns as $mandatoryColumn) {
            if (!in_array($mandatoryColumn, $this->colIndex)) {
                $missingColumns[] = $mandatoryColumn;
            }
        }
        if (count($missingColumns) > 0) {
            fclose($this->fileHandle);
            $this->errors[] = 'Folgende Pflichtfelder fehlen in der Datei: ' . implode(', ', $missingColumns);
            return;
        }

        // import further lines as hydraulic tubes
        while (!feof($this->fileHandle)) {

            // read fields and skip empty lines
            $fields = $this->readLine();
            if (false === $fields) {
                return;
            }

            // use column index as key for current values and check for mismatch
            try {
                $fields = array_combine($this->colIndex, $fields);
            } catch (\ValueError $error) {
                $this->errors[] = 'Zeile #' . $this->lineNumber . ': Anzahl der Spalten stimmt nicht mit erster Zeile überein.';
                continue;
            }

            // parse line
            try {
                $this->parseLine($fields);
            }
            catch (\Exception $e) {
                $this->errors[] = $e->getMessage();
            }
        }

        // close file
        fclose($this->fileHandle);
    }

    /**
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
     * @author W3C
     * @see https://www.w3.org/International/questions/qa-forms-utf-8.en.html
     * @param string $value
     * @return bool
     */
    static public function detectUTF8(string $value): bool
    {
        return preg_match('%^(?:
				  [\x09\x0A\x0D\x20-\x7E]            # ASCII
				| [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
				|  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
				| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
				|  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
				|  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
				| [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
				|  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
			)*$%xs', $value);
    }

    /**
     * @param array $fields
     */
    abstract protected function parseLine(array $fields);

    /**
     * @param string $value
     * @return int|null
     */
    protected function parseIntValue(string $value)
    {
        $value = str_replace(
            ['.', ','],
            ['', '.'],
            trim($value, "\r\n\t €°.nNvoVO")
        );
        return $value !== '' ? intval($value) : null;
    }

    /**
     * @param string $value
     * @return float|null
     */
    protected function parseFloatValue(string $value)
    {
        $value = str_replace(
            ['.', ','],
            ['', '.'],
            trim($value, "\r\n\t €°.nNvoVO")
        );
        return $value !== '' ? floatval($value) : null;
    }

    /**
     * @param string $value
     * @return string
     */
    protected function parseStringValue(string $value): string
    {
        $value = str_replace(
            ["\r\n", "\r", "\t"],
            ["\n", "\n", " "],
            trim($value, "\r\n\t")
        );
        return $value;
    }

    /**
     * @param string $value
     * @return \DateTimeImmutable|null
     */
    protected function parseDateTimeValue(string $value)
    {
        if (trim($value) == '') {
            return null;
        }
        $dateTime = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $value);
        if (false === $dateTime) {
            $dateTime = \DateTimeImmutable::createFromFormat('d.m.Y H:i', $value);
        }
        if (false === $dateTime) {
            $dateTime = \DateTimeImmutable::createFromFormat('d.m.Y H:i:s', $value);
        }
        if (false === $dateTime) {
            $dateTime = \DateTimeImmutable::createFromFormat('Y-m-d', $value);
        }
        if (false === $dateTime) {
            $dateTime = \DateTimeImmutable::createFromFormat('d.m.Y', $value);
        }

        // bugfix, correct two digit dates
        if ($dateTime->format('Y') < 100) {
            $dateTime = $dateTime->add(new \DateInterval('P2000Y'));
        }

        return $dateTime === false ? null : $dateTime;
    }

    /**
     * @param string $value
     * @return bool
     */
    protected function parseBoolValue(string $value): bool
    {
        $value = strtolower(trim($value));
        if ($value == 1 || $value == 'x' || $value == 'ja' || $value == 'wahr' || $value == 'true') {
            return true;
        }
        //if ($value == 0 || $value == '' || $value == 'nein' || $value == 'falsch' || $value == 'false') {
        //    return false;
        //}
        return false;
    }
}
