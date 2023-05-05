<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Service\Math\Calculator;

class ElementAreaRangeValue implements ElementValue
{

    /**
     * @var float
     */
    protected $minimumWidth;

    /**
     * @var float
     */
    protected $maximumWidth;

    /**
     * @var float
     */
    protected $stepWidth;

    /**
     * @var float
     */
    protected $minimumHeight;

    /**
     * @var float
     */
    protected $maximumHeight;

    /**
     * @var float
     */
    protected $stepHeight;

    /**
     * @var array
     */
    protected $prices;


    /**
     * ElementAreaRangeValue constructor.
     * @param float $minimumWidth
     * @param float $maximumWidth
     * @param float $stepWidth
     * @param float $minimumHeight
     * @param float $maximumHeight
     * @param float $stepHeight
     * @param array $prices
     */
    public function __construct(float $minimumWidth = 0.0, float $maximumWidth = 0.0, float $stepWidth = 1.0, float $minimumHeight = 0.0, float $maximumHeight = 0.0, float $stepHeight = 1.0, array $prices = [])
    {
        $this->minimumWidth = $minimumWidth;
        $this->maximumWidth = $maximumWidth;
        $this->stepWidth = $stepWidth;
        $this->minimumHeight = $minimumHeight;
        $this->maximumHeight = $maximumHeight;
        $this->stepHeight = $stepHeight;
        $this->prices = $prices;

        $this->assertMinimumLessOrEqualMaximum();
        $this->assertValidStep();
    }

    /**
     * @return float
     */
    public function getMinimumWidth(): float
    {
        return $this->minimumWidth;
    }

    /**
     * @return float
     */
    public function getMaximumWidth(): float
    {
        return $this->maximumWidth;
    }

    /**
     * @return float
     */
    public function getStepWidth(): float
    {
        return $this->stepWidth;
    }

    /**
     * @return float
     */
    public function getMinimumHeight(): float
    {
        return $this->minimumHeight;
    }

    /**
     * @return float
     */
    public function getMaximumHeight(): float
    {
        return $this->maximumHeight;
    }

    /**
     * @return float
     */
    public function getStepHeight(): float
    {
        return $this->stepHeight;
    }


    /**
     * @return array
     */
    public function getPrices(): array
    {
        return $this->prices;
    }

    /**
     * @return array
     */
    public function jsonEncode(): array
    {
        return [
            'class' => get_class($this),
            'json' => [
                'minimumWidth' => $this->minimumWidth,
                'maximumWidth' => $this->maximumWidth,
                'stepWidth' => $this->stepWidth,
                'minimumHeight' => $this->minimumHeight,
                'maximumHeight' => $this->maximumHeight,
                'stepHeight' => $this->stepHeight,
                'prices' => $this->prices
            ]
        ];
    }

    /**
     * @param array $json
     * @return ElementValue
     */
    public static function jsonDecode(array $json): ElementValue
    {
        if (self::class !==  $json['class']) {
            throw new \InvalidArgumentException('Cannot convert json value to Type \'ElementRangeValue\' due to wrong class namespace.');
        }
        if (!isset($json['json']['minimumWidth']) || !isset($json['json']['maximumWidth']) || !isset($json['json']['stepWidth']) || !isset($json['json']['minimumHeight']) || !isset($json['json']['maximumHeight']) || !isset($json['json']['stepHeight'])) {
            throw new \InvalidArgumentException('Cannot convert json value to Type \'ElementAreaRangeValue\' due to missing values.');
        }

        return new self (
            (float)$json['json']['minimumWidth'],
            (float)$json['json']['maximumWidth'],
            (float)$json['json']['stepWidth'],
            (float)$json['json']['minimumHeight'],
            (float)$json['json']['maximumHeight'],
            (float)$json['json']['stepHeight'],
            (array)$json['json']['prices']
        );
    }

    /**
     * The minimum set must be less or equal the maximum
     */
    protected function assertMinimumLessOrEqualMaximum()
    {
        if ($this->minimumWidth > $this->maximumWidth || $this->minimumHeight > $this->minimumHeight) {
            throw new \InvalidArgumentException('The given minimum must be less or equal the given maximum.');
        }
    }

    /**
     * The step must be greater than zero
     */
    protected function assertValidStep()
    {
        if ($this->stepWidth <= 0 || $this->stepHeight <= 0) {
            throw new \InvalidArgumentException('The given step must be greater than zero.');
        }
    }

    /**
     * @param $value
     * @return array
     */
    public function getValueLowerThan($value)
    {
        $width = $value['width'];
        $height = $value['height'];

        $result = [];
        $result['width'] = null;
        $result['height'] = null;

        $width = (float)$width;
        $height = (float)$height;

        $continue = true;

        // value outside of range
        if ($width < $this->minimumWidth || $width > $this->maximumWidth) {
            $continue = false;
        }

        if($continue) {
            $offset = $width - $this->minimumWidth;
            if (self::modulo($offset, $this->stepWidth) == 0) {
                $offset -= $this->stepWidth;
            } else {
                $offset -= self::modulo($offset, $this->stepWidth);
            }

            $resultValue = $this->minimumWidth + $offset;
            if ($resultValue < $this->minimumWidth || $resultValue > $this->maximumWidth) {
                $result['width'] = null;
            } else {
                $result['width'] = $resultValue;
            }
        }

        $continue = true;

        if ($height < $this->minimumHeight || $height > $this->maximumHeight) {
            $continue = false;
        }

        if($continue) {
            $offset = $height - $this->minimumHeight;
            if (self::modulo($offset, $this->stepHeight) == 0) {
                $offset -= $this->stepHeight;
            } else {
                $offset -= self::modulo($offset, $this->stepHeight);
            }

            $resultValue = $this->minimumHeight + $offset;
            if ($resultValue < $this->minimumHeight || $resultValue > $this->maximumHeight) {
                $result['height'] = null;
            } else {
                $result['height'] = $resultValue;
            }
        }
        return $result;
    }

    /**
     * @param $value
     * @return array
     */
    public function getValueGreaterThan($value)
    {
        $width = $value['width'];
        $height = $value['height'];

        $result = [];
        $result['width'] = null;
        $result['height'] = null;

        $width = (float)$width;
        $height = (float)$height;

        $continue = true;

        // value outside of range
        if ($width < $this->minimumWidth || $width > $this->maximumWidth) {
            $continue = false;
        }

        if($continue) {
            $offset = $width - $this->minimumWidth;
            if (self::modulo($offset, $this->stepWidth) == 0) {
                $offset += $this->stepWidth;
            } else {
                $offset -= self::modulo($offset, $this->stepWidth) - $this->stepWidth;
            }

            $resultValue = $this->minimumWidth + $offset;
            if ($resultValue < $this->minimumWidth || $resultValue > $this->maximumWidth) {
                $result['width'] = null;
            } else {
                $result['width'] = $resultValue;
            }
        }

        $continue = true;

        if ($height < $this->minimumHeight || $height > $this->maximumHeight) {
            $continue = false;
        }

        if($continue) {
            $offset = $height - $this->minimumHeight;
            if (self::modulo($offset, $this->stepHeight) == 0) {
                $offset += $this->stepHeight;
            } else {
                $offset -= self::modulo($offset, $this->stepHeight) - $this->stepHeight;
            }

            $resultValue = $this->minimumHeight + $offset;
            if ($resultValue < $this->minimumHeight || $resultValue > $this->maximumHeight) {
                $result['height'] = null;
            } else {
                $result['height'] = $resultValue;
            }
        }

        return $result;
    }

    /**
     * @param $value
     * @return array
     */
    public function getValueEqualTo($value)
    {
        $width = $value['width'];
        $height = $value['height'];

        $result = [];
        $result['width'] = null;
        $result['height'] = null;

        $width = (float)$width;
        $height = (float)$height;

        $continue = true;

        // value outside of range
        if ($width < $this->minimumWidth || $width > $this->maximumWidth) {
            $continue = false;
        }

        if($continue) {
            $offset = $width - $this->minimumWidth;
            if (self::modulo($offset, $this->stepWidth) == 0) {
                $result['width'] = $width;
            }
        }

        $continue = true;

        // value outside of range
        if ($height < $this->minimumWidth || $height > $this->maximumWidth) {
            $continue = false;
        }

        if($continue) {
            $offset = $height - $this->minimumWidth;
            if (self::modulo($offset, $this->stepWidth) == 0) {
                $result['height'] = $height;
            }
        }

        return $result;
    }

    /**
     * @param $value
     * @return array
     */
    public function getValueNotEqualTo($value)
    {
        $result = [];
        $result['width'] = null;
        $result['height'] = null;

        $min = $this->getValueLowerThan($value);
        if (null !== $min['width']) {
            $result['width'] = $min['width'];
        }
        if (null !== $min['height']) {
            $result['height'] = $min['height'];
        }

        $max = $this->getValueGreaterThan($value);
        if (null !== $max['width']) {
            $result['width'] = $max['width'];
        }
        if (null !== $max['height']) {
            $result['height'] = $max['height'];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getAnyValue()
    {
        $result = [];
        $result['maximumWidth'] = $this->maximumWidth;
        $result['minimumWidth'] = $this->minimumWidth;
        $result['stepWidth'] = $this->stepWidth;
        $result['maximumHeight'] = $this->maximumHeight;
        $result['minimumHeight'] = $this->minimumHeight;
        $result['stepHeight'] = $this->stepHeight;
        $result['prices'] = $this->prices;
        return $result;
    }

    /**
     * @param $value
     * @return bool
     */
    public function contains($value): bool
    {
        return $this->containsSingle(
            $value['width'],
            $this->minimumWidth,
            $this->maximumWidth,
            $this->stepWidth
        ) && $this->containsSingle(
            $value['height'],
            $this->minimumHeight,
            $this->maximumHeight,
            $this->stepHeight
        );
    }

    /**
     * @param $value
     * @param $minimum
     * @param $maximum
     * @param $step
     * @return bool
     */
    private function containsSingle($value, $minimum, $maximum, $step): bool
    {
        if (is_array($value)) {
            return false;
        }

        // calculator instance
        $calculator = new Calculator();

        // values to string
        $value = (string) $value;
        $min = (string) $minimum;
        $max = (string) $maximum;
        $step = (string) $step;

        // value outside of range
        if ($calculator->lt($value, $min) || $calculator->gt($value, $max)) {
            return false;
        }

        $offset = $calculator->sub($value, $min);
        $mod = $calculator->mod($offset, $step);

        return $calculator->eq($mod, '0');
    }


    /**
     * @param string $a
     * @param string $b
     * @return string
     */
    protected static function modulo(string $a, string $b)
    {
        // calculator instance
        $calculator = new Calculator();

        // calculate modulo: ($a - $b * floor($a / $b))
        return $calculator->sub(
            $a, $calculator->mul(
                $b, $calculator->floor(
                    $calculator->div($a, $b)
                )
            )
        );
    }

    /**
     * @return array
     */
    function jsonSerialize()
    {
        return [
            'type' => 'areaRange',
            'minimumWidth' => $this->getMinimumWidth(),
            'maximumWidth' => $this->getMaximumWidth(),
            'stepWidth' => $this->getStepWidth(),
            'minimumHeight' => $this->getMinimumHeight(),
            'maximumHeight' => $this->getMaximumHeight(),
            'stepHeight' => $this->getStepHeight(),
            'prices' => $this->getPrices()
        ];
    }
}
