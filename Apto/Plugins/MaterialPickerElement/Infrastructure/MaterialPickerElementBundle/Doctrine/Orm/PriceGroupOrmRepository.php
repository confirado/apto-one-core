<?php

namespace Apto\Plugins\MaterialPickerElement\Infrastructure\MaterialPickerElementBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\PriceGroup\PriceGroup;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\PriceGroup\PriceGroupRepository;

class PriceGroupOrmRepository extends AptoOrmRepository implements PriceGroupRepository
{
    const ENTITY_CLASS = PriceGroup::class;

    /**
     * @param PriceGroup $model
     */
    public function update(PriceGroup $model)
    {
        /** @phpstan-ignore-next-line */
        $this->_em->merge($model);
    }

    /**
     * @param PriceGroup $model
     */
    public function add(PriceGroup $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param PriceGroup $model
     */
    public function remove(PriceGroup $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param string $id
     * @return PriceGroup|null
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('PriceGroup')
            ->where('PriceGroup.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $internalName
     * @return string|null
     */
    public function findFirstIdByInternalName(string $internalName)
    {
        // create query
        $builder = $this
            ->createQueryBuilder('PriceGroup')
            ->select('PriceGroup.id.id as id, PriceGroup.internalName')
            ->where('PriceGroup.internalName LIKE :internalName')
            ->setParameter('internalName', '%' . $internalName . '%')
        ;

        // search for internalName match
        foreach ($builder->getQuery()->getScalarResult() as $priceGroup) {
            $translations = json_decode($priceGroup['internalName'], true);

            if (!is_array($translations)) {
                continue;
            }

            foreach ($translations as $translation) {
                if ($translation === $internalName) {
                    return $priceGroup['id'];
                }
            }
        }

        // return null if no internalName matched
        return null;
    }
}
