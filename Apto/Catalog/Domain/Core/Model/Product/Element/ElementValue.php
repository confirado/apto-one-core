<?php

namespace Apto\Catalog\Domain\Core\Model\Product\Element;

use Apto\Base\Domain\Core\Model\AptoJsonSerializable;

interface ElementValue extends AptoJsonSerializable, \JsonSerializable
{
    /**
     * @param $value
     * @return mixed|null
     */
    public function getValueLowerThan($value);

    /**
     * @param $value
     * @return mixed|null
     */
    public function getValueGreaterThan($value);

    /**
     * @param $value
     * @return mixed|null
     */
    public function getValueEqualTo($value);

    /**
     * @param $value
     * @return mixed|null
     */
    public function getValueNotEqualTo($value);

    /**
     * @return mixed|null
     */
    public function getAnyValue();

    /**
     * @param $value
     * @return bool
     */
    public function contains($value): bool;

}