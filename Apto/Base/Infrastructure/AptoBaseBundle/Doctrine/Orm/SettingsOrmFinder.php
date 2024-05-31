<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Application\Core\Query\Settings\SettingsFinder;
use Apto\Base\Domain\Core\Model\Settings\Settings;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class SettingsOrmFinder extends AptoOrmFinder implements SettingsFinder
{
    const ENTITY_CLASS = Settings::class;

    /**
     * @return array|null
     * @throws DqlBuilderException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findSettings(): ?array
    {
        $builder = new DqlPaginatorBuilder($this->entityClass);
        $builder
            ->setValues([
                's' => [
                    ['id.id', 'id'],
                    'created',
                    'primaryColor',
                    'secondaryColor',
                    'backgroundColorHeader',
                    'fontColorHeader',
                    'backgroundColorFooter',
                    'fontColorFooter'
                ]
            ])
            ->setOrderBy([
                ['s.created', 'ASC']
            ]);

        $result = $builder->getResult($this->entityManager);
        if (array_key_exists('data', $result) && count($result['data']) > 0) {
            return $result['data'][0];
        }
        return null;
    }
}
