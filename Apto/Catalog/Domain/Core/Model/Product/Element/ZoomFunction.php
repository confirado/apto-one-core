<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

use InvalidArgumentException;

final class ZoomFunction
{
    const DEACTIVATED = 'deactivated';
    const IMAGE_PREVIEW = 'image_preview';
    const GALLERY = 'gallery';
    const POSSIBLE_VALUES = [self::DEACTIVATED, self::IMAGE_PREVIEW, self::GALLERY];

    /**
     * @var string
     */
    protected string $value;

    /**
     * @param string $value
     */
    public function __construct(string $value = 'deactivated')
    {
        if (!in_array($value, self::POSSIBLE_VALUES)) {
            throw new InvalidArgumentException('Value must be one of the following: ' . implode(',',self::POSSIBLE_VALUES) . '.');
        }
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setValue(string $value): self
    {
        return new self($value);
    }

    /**
     * @param ZoomFunction $zoomFunction
     * @return bool
     */
    public function equals(ZoomFunction $zoomFunction): bool
    {
        return $zoomFunction->getValue() === $this->getValue();
    }
}
