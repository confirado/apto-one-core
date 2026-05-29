<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Doctrine\ORM\NonUniqueResultException;
use Apto\Base\Domain\Core\Model\Settings\Settings;
use Apto\Base\Domain\Core\Model\Settings\SettingsRepository;

class SettingsOrmRepository extends AptoOrmRepository implements SettingsRepository
{
    const ENTITY_CLASS = Settings::class;

    /**
     * @param Settings $model
     * @return void
     */
    public function update(Settings $model): void
    {
        /** @phpstan-ignore-next-line */
        $this->_em->merge($model);
    }

    /**
     * @param Settings $model
     * @return void
     */
    public function add(Settings $model): void
    {
        $this->_em->persist($model);
    }

    /**
     * @param string $id
     * @return Settings|null
     * @throws NonUniqueResultException
     */
    public function findById(string $id): ?Settings
    {
        $builder = $this->createQueryBuilder('Settings')
            ->where('Settings.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }
}
