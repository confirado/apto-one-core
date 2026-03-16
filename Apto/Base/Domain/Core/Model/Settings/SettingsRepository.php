<?php

namespace Apto\Base\Domain\Core\Model\Settings;

use Apto\Base\Domain\Core\Model\AptoRepository;

interface SettingsRepository extends AptoRepository
{
    /**
     * @param Settings $model
     * @return void
     */
    public function update(Settings $model): void;

    /**
     * @param Settings $model
     * @return void
     */
    public function add(Settings $model): void;

    /**
     * @param string $id
     * @return Settings|null
     */
    public function findById(string $id): ?Settings;

}
