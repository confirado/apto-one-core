<?php

namespace Apto\Catalog\Application\Core\Service\TaxCalculator;

use Apto\Base\Domain\Core\Service\Math\Calculator;
use Money\Money;

class SimpleTaxCalculator implements TaxCalculator
{
    /**
     * @var string
     */
    protected $tax;

    /**
     * input brutto
     * @var bool
     */
    protected $inputGross;

    /**
     * zeige brutto
     * @var bool
     */
    protected $showGross;

    /**
     * @var bool
     */
    protected $taxFree;

    /**
     * SimpleTaxCalculator constructor.
     * @param string $tax
     * @param bool $inputGross
     * @param bool $showGross
     * @throws \Apto\Base\Domain\Core\Service\Math\DivisionByZeroException
     */
    public function __construct(string $tax, bool $inputGross, bool $showGross)
    {
        $calculator = new Calculator();
        $this->tax = $calculator->add('1', $calculator->div($tax, '100'));
        $this->inputGross = $inputGross;
        $this->showGross = $showGross;
        $this->taxFree = false;
    }

    /**
     * @param Money $price
     * @return Money
     */
    public function getDisplayPrice(Money $price): Money
    {
        if ($this->showGross && !$this->inputGross) {
            // zeige brutto und input netto
            return $this->addTax($price);
        } else if (!$this->showGross && $this->inputGross) {
            // zeige netto und input brutto
            return $this->subTax($price);
        }
        return $price;
    }

    /**
     * @param Money $displayPrice
     * @return Money
     */
    public function getNetPriceFromDisplayPrice(Money $displayPrice): Money
    {
        if ($this->showGross) {
            // zeige brutto
            return $this->subTax($displayPrice);
        }
        return $displayPrice;
    }

    /**
     * @param Money $displayPrice
     * @return Money
     */
    public function getGrossPriceFromDisplayPrice(Money $displayPrice): Money
    {
        if (!$this->showGross) {
            // zeige brutto
            return $this->addTax($displayPrice);
        }
        return $displayPrice;
    }

    /**
     * @param Money $price
     * @return Money
     */
    public function addTax(Money $price): Money
    {
        if ($this->isTaxFree()) {
            return $price;
        }
        return $price->multiply($this->tax);
    }

    /**
     * @param Money $price
     * @return Money
     */
    public function subTax(Money $price): Money
    {
        return $price->divide($this->tax);
    }

    /**
     * @return string
     */
    public function getTax(): string
    {
        return $this->tax;
    }

    /**
     * @return bool
     */
    public function isInputGross(): bool
    {
        return $this->inputGross;
    }

    /**
     * @return bool
     */
    public function isShowGross(): bool
    {
        return $this->showGross;
    }

    /**
     * @return bool
     */
    public function isTaxFree(): bool
    {
        return $this->taxFree;
    }

    /**
     * @param bool $taxFree
     */
    public function setTaxFree(bool $taxFree): void
    {
        $this->taxFree = $taxFree;
    }
}