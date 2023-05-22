<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Catalog\Domain\Core\Model\PriceMatrix\PriceMatrix;
use Apto\Catalog\Domain\Core\Model\PriceMatrix\PriceMatrixRepository;

class PriceMatrixOrmRepository extends AptoOrmRepository implements PriceMatrixRepository
{
    const ENTITY_CLASS = PriceMatrix::class;

    /**
     * @param PriceMatrix $model
     */
    public function update(PriceMatrix $model)
    {
        /** @phpstan-ignore-next-line */
        $this->_em->merge($model);
    }

    /**
     * @param PriceMatrix $model
     */
    public function add(PriceMatrix $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param PriceMatrix $model
     */
    public function remove(PriceMatrix $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param string $id
     * @return PriceMatrix|null
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('PriceMatrix')
            ->where('PriceMatrix.id.id = :id')
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
            ->createQueryBuilder('PriceMatrix')
            ->select('PriceMatrix.id.id as id, PriceMatrix.name')
            ->where('PriceMatrix.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
        ;

        // search for name match
        foreach ($builder->getQuery()->getScalarResult() as $priceMatrix) {
            $translations = json_decode($priceMatrix['name'], true);

            if (!is_array($translations)) {
                continue;
            }

            foreach ($translations as $translation) {
                if ($translation === $name) {
                    return $priceMatrix['id'];
                }
            }
        }

        // return null if no name matched
        return null;
    }
}
