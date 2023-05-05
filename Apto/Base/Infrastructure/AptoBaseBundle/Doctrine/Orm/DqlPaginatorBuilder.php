<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Doctrine\ORM\EntityManagerInterface;

class DqlPaginatorBuilder extends DqlBuilder
{

    /**
     * @var int
     */
    protected $pageNumber = 1;

    /**
     * @var int
     */
    protected $recordsPerPage = 50;

    /**
     * @param int $pageNumber
     * @return DqlPaginatorBuilder
     */
    public function setPage(int $pageNumber): DqlPaginatorBuilder
    {
        $this->pageNumber = $pageNumber;
        return $this;
    }

    /**
     * @param int $recordsPerPage
     * @return DqlPaginatorBuilder
     */
    public function setRecordsPerPage(int $recordsPerPage): DqlPaginatorBuilder
    {
        $this->recordsPerPage = $recordsPerPage;
        return $this;
    }

    /**
     * @param EntityManagerInterface $em
     * @param bool $debug
     * @return DqlPaginator
     * @throws DqlBuilderException
     */
    public function getDqlPaginator(EntityManagerInterface $em, bool $debug = false): DqlPaginator
    {
        $dqlPaginator = new DqlPaginator(
            $em,
            $this->getDqlNumberOfRecords(),
            $this->getDqlSurrogateIds(),
            $this->getDqlRecords(),
            $debug
        );

        return $dqlPaginator;
    }

    /**
     * @param EntityManagerInterface $em
     * @param bool $debug
     * @param bool $scalar
     * @return array
     * @throws DqlBuilderException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getResult(EntityManagerInterface $em, bool $debug = false, bool $scalar = false): array
    {
        $dqlPaginator = $this->getDqlPaginator($em, $debug);
        $pageResult = $dqlPaginator->setParameters($this->parameters)->getPage($this->pageNumber, $this->recordsPerPage);

        if ($scalar === false) {
            $result = [
                'numberOfRecords' => $pageResult['numberOfRecords'],
                'numberOfPages' => $pageResult['numberOfPages'],
                'data' => $this->convertToNestedResults($pageResult['records'])
            ];
        } else {
            $result = [
                'numberOfRecords' => $pageResult['numberOfRecords'],
                'numberOfPages' => $pageResult['numberOfPages'],
                'data' => $pageResult['records']
            ];
        }

        if ($debug) {
            $result['sql'] = $dqlPaginator->getSql();
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getEmptyResult()
    {
        return [
            'numberOfRecords' => 0,
            'numberOfPages' => 0,
            'data' => []
        ];
    }

    /**
     * @return string
     */
    protected function getDqlNumberOfRecords(): string
    {
        $select = 'SELECT COUNT(DISTINCT ' . $this->root . '.' . $this->idName . ')';
        $from = ' FROM ' . $this->entityClass . ' ' . $this->root;
        $join = $this->getDqlJoins();
        $where = $this->getDqlWhere();

        return $select . $from . $join . $where;
    }

    /**
     * @return string
     */
    protected function getDqlSurrogateIds(): string
    {
        $select = 'SELECT DISTINCT ' . $this->root . '.' . $this->idName;

        foreach ($this->orderBy as $orderBy) {
            $orderBy = ((array)$orderBy)[0];
            list($root, $value) = explode('.', $orderBy, 2);
            if ($root !== $this->root || $value !== $this->idName) {
                $select .= ', ' . $orderBy . ' ' . $this->getAlias($value, $root);
            }
        }

        $from = ' FROM ' . $this->entityClass . ' ' . $this->root;
        $join = $this->getDqlJoins();
        $where = $this->getDqlWhere();
        $order = '';
        if ($this->orderBy) {
            $order = ' ORDER BY ' . $this->getDqlOrderByValues();
        }

        return $select . $from . $join . $where . $order;
    }

    /**
     * @return string
     * @throws DqlBuilderException
     */
    protected function getDqlRecords(): string
    {
        $select = 'SELECT ' . $this->getDqlSelectValues();
        $from = ' FROM ' . $this->entityClass . ' ' . $this->root;
        $join = $this->getDqlJoins();
        $where = ' WHERE ' . $this->root . '.' . $this->idName . ' IN (:surrogateIds)';
        $order = '';
        if ($this->orderBy) {
            $order = ' ORDER BY ' . $this->getDqlOrderByValues();
        }

        return $select . $from . $join . $where . $order;
    }

}