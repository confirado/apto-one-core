<?php

namespace Apto\Base\Domain\Core\Model;

/**
 * Class Color
 * @todo implement color distance calculation, see https://github.com/hasbridge/php-color/blob/master/Color.php
 * @package Apto\Base\Domain\Core\Model
 */
final class Color implements \JsonSerializable
{
    /**
     * @var int
     */
    private $red;

    /**
     * @var int
     */
    private $green;

    /**
     * @var int
     */
    private $blue;

    /**
     * @param string $color
     * @return Color
     */
    public static function fromHex(string $color): Color
    {
        $sign = substr($color, 0, 1);
        $red = substr($color, 1, 2);
        $green = substr($color, 3, 2);
        $blue = substr($color, 5, 2);

        if (
            $sign !== '#' || strlen($red) !== 2 || strlen($green) !== 2 || strlen($blue) !== 2
        ) {
            throw new \InvalidArgumentException('The given hex code \'' . $color . '\' is not a valid color code.');
        }

        $red = hexdec($red);
        $green = hexdec($green);
        $blue = hexdec($blue);

        return new self($red, $green, $blue);
    }

    /**
     * @param string $color
     * @return Color
     */
    public static function fromJson(string $color): Color
    {
        $color = json_decode($color, true);
        if (
            !array_key_exists('r', $color) || !array_key_exists('g', $color) || !array_key_exists('b', $color)
        ) {
            throw new \InvalidArgumentException('The given json is not valid.');
        }

        return new self($color['r'], $color['g'], $color['b']);
    }

    /**
     * Color constructor.
     * @param int $red
     * @param int $green
     * @param int $blue
     */
    public function __construct(int $red, int $green, int $blue)
    {
        if (
            $red < 0 || $red > 255 ||
            $green < 0 || $green > 255 ||
            $blue < 0 || $blue > 255
        ) {
            throw new \InvalidArgumentException('Values for red, green and blue must be between 0 and 255');
        }

        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;
    }

    /**
     * @return int
     */
    public function getRed(): int
    {
        return $this->red;
    }

    /**
     * @return int
     */
    public function getGreen(): int
    {
        return $this->green;
    }

    /**
     * @return int
     */
    public function getBlue(): int
    {
        return $this->blue;
    }

    /**
     * @return array
     */
    public function getRgb(): array
    {
        return [
            'r' => $this->red,
            'g' => $this->green,
            'b' => $this->blue
        ];
    }

    /**
     * @return string
     */
    public function getHex(): string
    {
        $hex = '#';
        $hex .= str_pad(dechex($this->red), 2, '0', STR_PAD_LEFT);
        $hex .= str_pad(dechex($this->green), 2, '0', STR_PAD_LEFT);
        $hex .= str_pad(dechex($this->blue), 2, '0', STR_PAD_LEFT);

        return $hex;
    }

    /**
     * @param Color $color
     * @return bool
     */
    public function equals(Color $color): bool
    {
        if (
            $this->getRed() === $color->getRed() &&
            $this->getGreen() === $color->getGreen() &&
            $this->getBlue() === $color->getBlue()
        ) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getHex();
    }

    /**
     * @return array
     */
    public function __toArray(): array
    {
        return $this->getRgb();
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->__toArray();
    }
}