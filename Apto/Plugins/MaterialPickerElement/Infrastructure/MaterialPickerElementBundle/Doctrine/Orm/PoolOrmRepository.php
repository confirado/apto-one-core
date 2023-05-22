<?php

namespace Apto\Plugins\MaterialPickerElement\Infrastructure\MaterialPickerElementBundle\Doctrine\Orm;

use Apto\Base\Application\Core\Service\AptoCache\AptoCacheService;
use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Pool\Pool;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Pool\PoolRepository;

class PoolOrmRepository extends AptoOrmRepository implements PoolRepository
{
    const ENTITY_CLASS = Pool::class;

    /**
     * @param Pool $model
     */
    public function update(Pool $model)
    {
        /** @phpstan-ignore-next-line */
        $this->_em->merge($model);
        $this->invalidateCache();
    }

    /**
     * @param Pool $model
     */
    public function add(Pool $model)
    {
        $this->_em->persist($model);
        $this->invalidateCache();
    }

    /**
     * @param Pool $model
     */
    public function remove(Pool $model)
    {
        $this->_em->remove($model);
        $this->invalidateCache();
    }

    /**
     * @param string $id
     * @return Pool|null
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('Pool')
            ->where('Pool.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function findFirstIdByName(string $name)
    {
        // create query
        $builder = $this
            ->createQueryBuilder('Pool')
            ->select('Pool.id.id as id, Pool.name')
            ->where('Pool.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
        ;

        // search for name match
        foreach ($builder->getQuery()->getScalarResult() as $pool) {
            $translations = json_decode($pool['name'], true);

            if (!is_array($translations)) {
                continue;
            }

            foreach ($translations as $translation) {
                if ($translation === $name) {
                    return $pool['id'];
                }
            }
        }

        // return null if no name matched
        return null;
    }

    /**
     * @return void
     */
    public function invalidateCache()
    {
        AptoCacheService::clearCache('PoolItemsFiltered-');
        AptoCacheService::clearCache('PoolColorItemsFiltered-');
    }
}
