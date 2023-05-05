<?php

namespace Apto\Catalog\Domain\Core\Model\Configuration;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface CodeConfigurationRepository extends AptoRepository
{
    /**
     * @param CodeConfiguration $model
     */
    public function add(CodeConfiguration $model);

    /**
     * @param CodeConfiguration $model
     */
    public function remove(CodeConfiguration $model);

    /**
     * @param $id
     * @return CodeConfiguration|null
     */
    public function findById($id);

    /**
     * @param string $id
     * @param string $code
     * @return bool
     */
    public function isCodeUnique(string $id, string $code): bool;
}