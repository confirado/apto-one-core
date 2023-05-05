<?php

namespace Apto\Base\Domain\Core\Model\Language;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface LanguageRepository extends AptoRepository
{
    /**
     * @param Language $model
     */
    public function update(Language $model);

    /**
     * @param Language $model
     */
    public function add(Language $model);

    /**
     * @param Language $model
     */
    public function remove(Language $model);

    /**
     * @param string $id
     * @return Language|null
     */
    public function findById($id);

    /**
     * @return array
     */
    public function findAll();

    /**
     * @param string $isocode
     * @return Language|null
     */
    public function findOneByIsocode($isocode);

    /**
     * @return int
     */
    public function countLanguages();
}