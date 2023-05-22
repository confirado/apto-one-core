<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Doctrine\Orm;

use Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm\AptoOrmRepository;
use Apto\Catalog\Domain\Core\Model\Configuration\CodeConfiguration;
use Apto\Catalog\Domain\Core\Model\Configuration\CodeConfigurationRepository;

class CodeConfigurationOrmRepository extends AptoOrmRepository implements CodeConfigurationRepository
{
    const ENTITY_CLASS = CodeConfiguration::class;

    /**
     * @param CodeConfiguration $model
     */
    public function add(CodeConfiguration $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param CodeConfiguration $model
     * @throws \Doctrine\ORM\ORMException
     */
    public function update(CodeConfiguration $model)
    {
        /** @phpstan-ignore-next-line */
        $this->_em->merge($model);
    }

    /**
     * @param CodeConfiguration $model
     */
    public function remove(CodeConfiguration $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param mixed $id
     * @return CodeConfiguration|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById($id)
    {
        $builder = $this->createQueryBuilder('CodeConfiguration')
            ->where('CodeConfiguration.id.id = :id')
            ->setParameter('id', $id);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $id
     * @param string $code
     * @return bool
     */
    public function isCodeUnique(string $id, string $code): bool
    {
        $entity = 'Apto\Catalog\Domain\Core\Model\Configuration\CodeConfiguration';

        $dql = 'SELECT c.id.id as id, c.code FROM ' . $entity . ' c WHERE c.code = :code AND c.id.id != :id';

        $result = $this->_em
            ->createQuery($dql)
            ->setParameters([
                'code' => $code,
                'id' => $id
            ])
            ->getResult()
        ;

        return count($result) > 0 ? false : true;
    }
}
