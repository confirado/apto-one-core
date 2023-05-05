<?php

namespace Apto\Catalog\Application\Core\Service\TaxCalculator;

use Money\Money;

/**
 * Interface TaxCalculator
 * @package Apto\Catalog\Application\Core\Service\TaxCalculator
 */
interface TaxCalculator
{
    /**
     * @param Money $price
     * @return Money
     */
    public function getDisplayPrice(Money $price): Money;

    /**
     * @param Money $price
     * @return Money
     */
    public function getNetPriceFromDisplayPrice(Money $price): Money;

    /**
     * @param Money $price
     * @return Money
     */
    public function getGrossPriceFromDisplayPrice(Money $price): Money;

    /**
     * @param Money $price
     * @return Money
     */
    public function addTax(Money $price): Money;

    /**
     * @param Money $price
     * @return Money
     */
    public function subTax(Money $price): Money;

    /**
     * @return string
     */
    public function getTax(): string;

    /**
     * @return bool
     */
    public function isInputGross(): bool;

    /**
     * @return bool
     */
    public function isShowGross(): bool;
}