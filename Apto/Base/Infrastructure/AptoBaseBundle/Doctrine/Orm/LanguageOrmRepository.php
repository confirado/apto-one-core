<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Domain\Core\Model\Language\Language;
use Apto\Base\Domain\Core\Model\Language\LanguageRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;

/**
 * LanguageRepository
 */
class LanguageOrmRepository extends AptoOrmRepository implements LanguageRepository
{
    const ENTITY_CLASS = Language::class;

    /**
     * @param Language $model
     * @throws ORMException
     */
    public function update(Language $model)
    {
        /** @phpstan-ignore-next-line */
        $this->_em->merge($model);
    }

    /**
     * @param Language $model
     * @throws ORMException
     */
    public function add(Language $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param Language $model
     * @throws ORMException
     */
    public function remove(Language $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param string $id
     * @return Language|null
     * @throws NonUniqueResultException
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('Language')
            ->where('Language.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $isocode
     * @return Language|null
     * @throws NonUniqueResultException
     */
    public function findOneByIsocode($isocode)
    {
        $builder = $this->createQueryBuilder('Language')
            ->where('Language.isocode.name = :isocode')
            ->setParameter('isocode', $isocode);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countLanguages()
    {
        $builder = $this->createQueryBuilder('Language')
            ->select('count(Language.id.id)');

        return $builder->getQuery()->getSingleScalarResult();
    }
}
