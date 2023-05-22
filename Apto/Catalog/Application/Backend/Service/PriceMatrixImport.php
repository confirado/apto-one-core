<?php

namespace Apto\Catalog\Application\Backend\Service;

use Apto\Base\Application\Backend\Service\AbstractCsvImport;
use Apto\Base\Domain\Core\Model\AptoCustomPropertyException;
use Apto\Base\Domain\Core\Model\CustomerGroup\CustomerGroup;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Domain\Core\Model\PriceMatrix\PriceMatrix;
use Apto\Catalog\Domain\Core\Model\PriceMatrix\PriceMatrixPosition;
use Money\Currency;
use Money\Money;

class PriceMatrixImport extends AbstractCsvImport
{
    /**
     * mandatory columns
     */
    protected array $mandatoryColumns = [
        'Breite',
        'Höhe',
        'Preis'
    ];

    /**
     * @var Currency|null
     */
    protected $currency;

    /**
     * @var CustomerGroup|null
     */
    protected $customerGroup;

    /**
     * @var PriceMatrix|null
     */
    protected $priceMatrix;

    /**
     * @var string
     */
    protected $csvType;

    /**
     * PriceMatrixImport constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->currency = null;
        $this->customerGroup = null;
        $this->priceMatrix = null;
        $this->csvType = 'flat';
    }

    /**
     * @param Currency $currency
     * @return PriceMatrixImport
     */
    public function setCurrency(Currency $currency): PriceMatrixImport
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @param CustomerGroup $customerGroup
     * @return PriceMatrixImport
     */
    public function setCustomerGroup(CustomerGroup $customerGroup): PriceMatrixImport
    {
        $this->customerGroup = $customerGroup;
        return $this;
    }

    /**
     * @param PriceMatrix $priceMatrix
     * @return PriceMatrixImport
     */
    public function setPriceMatrix(PriceMatrix $priceMatrix): PriceMatrixImport
    {
        $this->priceMatrix = $priceMatrix;
        return $this;
    }

    /**
     * @param string $csvType
     * @return PriceMatrixImport
     */
    public function setCsvType(string $csvType): PriceMatrixImport
    {
        $this->csvType = $csvType;
        return $this;
    }

    /**
     * @param string[] $mandatoryColumns
     * @return PriceMatrixImport
     */
    public function setMandatoryColumns(array $mandatoryColumns): PriceMatrixImport
    {
        $this->mandatoryColumns = $mandatoryColumns;
        return $this;
    }

    /**
     * @param File $file
     */
    public function importCsvFile(File $file)
    {
        if (null === $this->currency) {
            throw new \InvalidArgumentException('Currency has not been set.');
        }
        if (null === $this->customerGroup) {
            throw new \InvalidArgumentException('CustomerGroup has not been set.');
        }
        if (null === $this->priceMatrix) {
            throw new \InvalidArgumentException('PriceMatrix has not been set.');
        }

        // remove old prices with same currency and customer group
        $this->priceMatrix->removePriceMatrixElementPricesByCustomerGroup($this->currency, $this->customerGroup->getId());

        // run csv import
        parent::importCsvFile($file);

        // remove PriceMatrixElements without prices
        $this->priceMatrix->removePriceMatrixElementsWithoutPrices();
    }

    /**
     * read line and import tube type
     * @param array $fields
     * @throws AptoCustomPropertyException
     * @throws InvalidUuidException
     */
    protected function parseLine(array $fields)
    {
        if ($this->csvType === 'matrix') {
            $this->parseMatrixLine($fields);
            return;
        }
        // get values
        $width = $this->parseFloatValue($fields['Breite']);
        $height = $this->parseFloatValue($fields['Höhe']);
        $amount = $this->parseFloatValue($fields['Preis']);

        // remove values from fields
        unset($fields['Breite']);
        unset($fields['Höhe']);
        unset($fields['Preis']);

        $this->addPrice($height, $width, $amount, $fields);
    }

    /**
     * read line and import tube type (Matrix Type)
     * @param array $fields
     * @throws AptoCustomPropertyException
     * @throws InvalidUuidException
     */
    private function parseMatrixLine(array $fields)
    {
        $height = null;
        foreach ($fields as $width => $field) {
            if ($height === null) {
                $height = $field;
                continue;
            }
            $width = $this->parseFloatValue($width);
            $height = $this->parseFloatValue($height);
            if (trim($field) === '') {
                continue;
            }
            $amount = $this->parseFloatValue($field);
            $this->addPrice($height, $width, $amount);
        }
    }

    /**
     * @param mixed $height
     * @param mixed $width
     * @param mixed $amount
     * @param array $customProperties
     * @throws AptoCustomPropertyException
     * @throws InvalidUuidException
     */
    private function addPrice($height, $width, $amount, array $customProperties = [])
    {
        // assert valid values
        if ($width < 0) {
            $this->errors[] = 'Zeile #' . $this->lineNumber . ': Ungültiges Format für Breite, Wert muss größer gleich 0 sein';
            return;
        }
        if ($height < 0) {
            $this->errors[] = 'Zeile #' . $this->lineNumber . ': Ungültiges Format für Höhe, Wert muss größer gleich 0 sein';
            return;
        }
        if ($amount < 0) {
            $this->errors[] = 'Zeile #' . $this->lineNumber . ': Ungültiges Format für Preis, Wert muss größer gleich 0 sein';
            return;
        }

        // create price object
        $price = new Money($amount * 100, $this->currency);

        // find existing PriceMatrixElement or create new one
        $position = new PriceMatrixPosition($width, $height);
        $elementId = $this->priceMatrix->getPriceMatrixElementIdByPosition($position);
        if (null === $elementId) {
            $elementId = $this->priceMatrix
                ->addPriceMatrixElement($position)
                ->getPriceMatrixElementIdByPosition($position);
        } else {
            // remove existing custom properties
            $this->priceMatrix->clearPriceMatrixElementCustomProperties($elementId);
        }

        // add price
        try {
            $this->priceMatrix->addPriceMatrixElementPrice($elementId, $price, $this->customerGroup->getId());
        } catch (\Exception $e) {
            $this->errors[] = 'Zeile #' . $this->lineNumber . ': Preis bereits durch eine vorherige Zeile definiert';
            return;
        }

        // add custom properties
        foreach ($customProperties as $key => $value) {
            if (strlen($value) > 0) {
                $this->priceMatrix->addPriceMatrixElementCustomProperty($elementId, $key, $value);
            }
        }
    }
}
