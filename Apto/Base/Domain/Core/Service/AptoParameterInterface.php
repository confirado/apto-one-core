<?php

namespace Apto\Base\Domain\Core\Service;

interface AptoParameterInterface
{
    /**
     * @param string $parameter
     * @return array|bool|string|int|float|null
     */
    public function get(string $parameter);

    /**
     * @param string $parameter
     * @return bool
     */
    public function has(string $parameter): bool;
}