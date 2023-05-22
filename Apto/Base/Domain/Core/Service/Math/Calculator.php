<?php

namespace Apto\Base\Domain\Core\Service\Math;

class Calculator
{
    /**
     * @var bool
     */
    protected $bcMath;

    /**
     * @var int
     */
    protected $scale;

    /**
     * Calculator constructor.
     * @param int $scale
     */
    public function __construct(int $scale = 14)
    {
        $this->scale = $scale;
        $this->bcMath = false;
        if (extension_loaded('bcmath')) {
            $this->bcMath = true;
        }
    }

    /**
     * @param string $a
     * @param string $b
     * @return string
     */
    public function mul(string $a, string $b): string
    {
        if ($this->bcMath) {
            return bcmul($a, $b, $this->scale);
        }

        return $this->nativeToString(
            $this->stringToNative($a) * $this->stringToNative($b)
        );
    }

    /**
     * @param array $numbers
     * @return string
     */
    public function mulList(array $numbers): string
    {
        if (count($numbers) < 1) {
            return '0';
        }

        $result = '1';
        foreach ($numbers as $number) {
            $result = $this->mul($result, $number);
        }
        return $result;
    }

    /**
     * @param string $a
     * @param string $b
     * @return string
     * @throws DivisionByZeroException
     */
    public function div(string $a, string $b): string
    {
        if ($b === '0') {
            throw new DivisionByZeroException('Uncaught DivisionByZeroException: Division by zero.');
        }

        if ($this->bcMath) {
            return bcdiv($a, $b, $this->scale);
        }

        return $this->nativeToString(
            $this->stringToNative($a) / $this->stringToNative($b)
        );
    }

    /**
     * @param string $a
     * @param string $b
     * @return string
     */
    public function add(string $a, string $b): string
    {
        if ($this->bcMath) {
            return bcadd($a, $b, $this->scale);
        }

        return $this->nativeToString(
            $this->stringToNative($a) + $this->stringToNative($b)
        );
    }

    /**
     * @param array $numbers
     * @return string
     */
    public function addList(array $numbers): string
    {
        $result = '0';
        foreach ($numbers as $number) {
            $result = $this->add($result, $number);
        }
        return $result;
    }

    /**
     * @param string $a
     * @param string $b
     * @return string
     */
    public function sub(string $a, string $b): string
    {
        if ($this->bcMath) {
            return bcsub($a, $b, $this->scale);
        }

        return $this->nativeToString(
            $this->stringToNative($a) - $this->stringToNative($b)
        );
    }

    /**
     * @param string $a
     * @return string
     */
    public function sqrt(string $a): string
    {
        if ($this->bcMath) {
            return bcsqrt($a, $this->scale);
        }
        return $this->nativeToString(
            sqrt($this->stringToNative($a))
        );
    }

    /**
     * @param string $dividend
     * @param string $modulus
     * @return string
     */
    public function mod(string $dividend, string $modulus)
    {
        // if modulus is zero, result would be undefined because division by zero is not allowed
        if ($this->eq($modulus, '0')) {
            throw new DivisionByZeroException('Uncaught DivisionByZeroException: Modulo by zero.');
        }

        // calculate modulo: ($dividend - $modulus * floor($dividend / $modulus))
        return $this->sub(
            $dividend, $this->mul(
            $modulus, $this->floor(
                    $this->div($dividend, $modulus)
                )
            )
        );
    }

    /**
     * @param string $base
     * @param string $exponent
     * @return string
     */
    public function pow(string $base, string $exponent): string
    {
        if ($this->bcMath) {
            return bcpow($base, $exponent, $this->scale);
        }

        return $this->nativeToString(
            pow(
                $this->stringToNative($base),
                $this->stringToNative($exponent)
            )
        );
    }

    /**
     * @todo handle bcpowmod return NULL if modulus is 0 or exponent is negative
     * @param string $base
     * @param string $exponent
     * @param string $modulus
     * @return string
     * @throws DivisionByZeroException
     */
    public function powMod(string $base, string $exponent, string $modulus): string
    {
        if ($this->bcMath) {
            return bcpowmod($base, $exponent, $modulus, $this->scale);
        }

        return $this->mod($this->pow($base, $exponent), $modulus);
    }

    /**
     * @param string $a
     * @param string $b
     * @return int
     */
    public function comp(string $a, string $b): int
    {
        if ($this->bcMath) {
            return bccomp($a, $b, $this->scale);
        }

        if ($this->stringToNative($a) < $this->stringToNative($b)) {
            return -1;
        }

        if ($this->stringToNative($a) > $this->stringToNative($b)) {
            return 1;
        }

        return 0;
    }

    /**
     * @param string $a
     * @param string $b
     * @return bool
     */
    public function eq(string $a, string $b): bool
    {
        return $this->comp($a, $b) === 0;
    }

    /**
     * @param string $a
     * @param string $b
     * @return bool
     */
    public function neq(string $a, string $b): bool
    {
        return $this->comp($a, $b) !== 0;
    }

    /**
     * @param string $a
     * @param string $b
     * @return bool
     */
    public function lt(string $a, string $b): bool
    {
        return $this->comp($a, $b) === -1;
    }

    /**
     * @param string $a
     * @param string $b
     * @return bool
     */
    public function gt(string $a, string $b): bool
    {
        return $this->comp($a, $b) === 1;
    }

    /**
     * @param string $a
     * @param string $b
     * @return bool
     */
    public function lte(string $a, string $b): bool
    {
        return $this->lt($a, $b) || $this->eq($a, $b);
    }

    /**
     * @param string $a
     * @param string $b
     * @return bool
     */
    public function gte(string $a, string $b): bool
    {
        return $this->gt($a, $b) || $this->eq($a, $b);
    }

    /**
     * @param string $number
     * @param int $precision
     * @return string
     */
    public function round(string $number, int $precision = 0): string
    {
        if ($this->bcMath) {
            return $this->bcRound($number, $precision);
        }

        return $this->nativeToString(
            round($this->stringToNative($number), $precision)
        );
    }

    /**
     * @param string $number
     * @return string
     */
    public function ceil(string $number): string
    {
        if ($this->bcMath) {
            return $this->bcCeil($number);
        }

        return $this->nativeToString(
            ceil($this->stringToNative($number))
        );
    }

    /**
     * @param string $number
     * @return string
     */
    public function floor(string $number): string
    {
        if ($this->bcMath) {
            return $this->bcFloor($number);
        }

        return $this->nativeToString(
            floor($this->stringToNative($number))
        );
    }

    /**
     * @param string $number
     * @return string
     */
    public function abs(string $number): string
    {
        if ($this->bcMath) {
            return $this->bcAbs($number);
        }

        return $this->nativeToString(
            abs($this->stringToNative($number))
        );
    }

    /**
     * round a bc math number half up with a precision of scale
     * @see http://php.net/manual/de/function.bcscale.php mwgamera´s post
     * @param string $number
     * @param int $scale
     * @return string
     */
    private function bcRound(string $number, int $scale = 0): string
    {
        $fix = '5';
        for ($i = 0; $i < $scale; $i++) {
            $fix = '0' . $fix;
        }

        if ($this->isNegative($number)) {
            $number = bcsub($number, '0.' . $fix, $scale + 1);
        } else {
            $number = bcadd($number, '0.' . $fix, $scale + 1);
        }

        return $this->scaleNumber($number, $scale);
    }

    /**
     * round a bc math number like php ceil function
     * @param string $number
     * @return string
     */
    private function bcCeil(string $number): string
    {
        if ($this->isNegative($number)) {
            return $this->bcCeilNegative($number);
        }
        return $this->bcCeilPositive($number);
    }

    /**
     * round a bc math positive number like php ceil function
     * @param string $number
     * @return string
     */
    private function bcCeilPositive(string $number): string
    {
        $floorNumber = $this->bcFloorPositive($number);

        if ($this->scaleNumber($floorNumber, $this->scale) === $this->scaleNumber($number, $this->scale)) {
            return $floorNumber;
        }

        return bcadd($number, '1.0', 0);
    }

    /**
     * round a bc math negative number like php ceil function
     * @param string $number
     * @return string
     */
    private function bcCeilNegative(string $number): string
    {
        return $this->scaleNumber($number, 0);
    }

    /**
     * round a bc math number like php floor function
     * @param string $number
     * @return string
     */
    private function bcFloor(string $number): string
    {
        if ($this->isNegative($number)) {
            return $this->bcFloorNegative($number);
        }
        return $this->bcFloorPositive($number);
    }

    /**
     * round a bc math positive number like php floor function
     * @param string $number
     * @return string
     */
    private function bcFloorPositive(string $number): string
    {
        return $this->scaleNumber($number, 0);
    }

    /**
     * round a bc math negative number like php floor function
     * @param string $number
     * @return string
     */
    private function bcFloorNegative(string $number): string
    {
        $ceilNumber = $this->bcCeilNegative($number);
        if ($this->scaleNumber($ceilNumber, $this->scale) === $this->scaleNumber($number, $this->scale)) {
            return $ceilNumber;
        }

        return bcsub($number, '1.0', 0);
    }

    /**
     * @param string $number
     * @return string
     */
    private function bcAbs(string $number): string
    {
        if ($this->isNegative($number)) {
            return $this->scaleNumber(substr($number, 1), $this->scale);
        }
        return bcdiv($number, '1.0', $this->scale);
    }

    /**
     * @param string $number
     * @return float|int
     */
    private function stringToNative(string $number)
    {
        return strpos($number, '.') === false ? (int) $number : (float) $number;
    }

    /**
     * @param int|float|mixed $number
     * @return string
     */
    private function nativeToString($number): string
    {
        if (is_int($number)) {
            return sprintf('%d', $number);
        }

        if (is_float($number)) {
            return sprintf('%.' . $this->scale . 'F', $number);
        }

        throw new \InvalidArgumentException('Native must be from type int ro float');
    }

    /**
     * @param string $number
     * @param int $scale
     * @return string
     */
    private function scaleNumber(string $number, int $scale): string
    {
        return bcdiv($number, '1.0', $scale);
    }

    /**
     * @param string $number
     * @return bool
     */
    private function isNegative(string $number): bool
    {
        return $number[0] === '-';
    }
}
