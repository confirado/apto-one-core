<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Doctrine\ORM\EntityManagerInterface;

class DqlQueryBuilder extends DqlBuilder
{
    /**
     * @param EntityManagerInterface $em
     * @return array|null
     * @throws DqlBuilderException
     */
    public function getSingleResultOrNull(EntityManagerInterface $em)
    {
        $dql = $this->getDqlRecords();
        $query = $em->createQuery($dql);
        if (!empty($this->parameters)) {
            $query->setParameters($this->parameters);
        }
        $results = $query->getScalarResult();

        if (0 == count($results)) {
            return null;
        }

        $result = $this->convertToNestedResults($results);

        return $result[0];
    }

    /**
     * @param string|array $property
     * @param mixed $value
     * @return DqlQueryBuilder
     */
    public function findByProperty($property, $value): DqlQueryBuilder
    {
        // append with AND, if where statement already exists
        if ('' != $this->where) {
            $this->where .= ' AND ';
        }

        $property = (array)$property;
        $root = count($property) > 1 ? $property[1] : $this->root;
        $parameterProperty = $this->getParameterId($property[0], $root);
        $this->where .= $root . '.' . $property[0] . ' = :' . $parameterProperty;
        $this->parameters[$parameterProperty] = $value;

        return $this;
    }

    /**
     * alias for findByProperty('id.id', $value)
     * @param mixed $value
     * @return DqlQueryBuilder
     */
    public function findById($value): DqlQueryBuilder
    {
        return $this->findByProperty('id.id', $value);
    }
}
