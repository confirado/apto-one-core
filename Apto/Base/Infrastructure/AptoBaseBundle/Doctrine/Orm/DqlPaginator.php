<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Doctrine\ORM\EntityManagerInterface;

class DqlPaginator
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var string
     */
    private $numberOfRecordsDql;

    /**
     * @var string
     */
    private $surrogateIdsDql;

    /**
     * @var string
     */
    private $recordsDql;

    /**
     * @var bool
     */
    private $debugSql;

    /**
     * @var array
     */
    private $sql = [];

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * DqlPaginator constructor.
     * @param EntityManagerInterface $entityManager
     * @param string $numberOfRecordsDql
     * @param string $surrogateIdsDql
     * @param string $recordsDql
     * @param bool $debugSql
     */
    public function __construct(EntityManagerInterface $entityManager, string $numberOfRecordsDql, string $surrogateIdsDql, string $recordsDql, bool $debugSql = false)
    {
        $this->entityManager = $entityManager;
        $this->numberOfRecordsDql = $numberOfRecordsDql;
        $this->surrogateIdsDql = $surrogateIdsDql;
        $this->recordsDql = $recordsDql;
        $this->debugSql = $debugSql;
    }

    /**
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @return array
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPage(int $pageNumber, int $recordsPerPage): array
    {
        $numberOfRecords = $this->getNumberOfRecordsResult();
        $surrogateIdsResult = $this->getSurrogateIdsResult($pageNumber, $recordsPerPage);

        if(empty($surrogateIdsResult)) {
            $recordsResult = [];
        }
        else {
            $recordsResult = $this->getRecordsResult($surrogateIdsResult);
        }

        $numberOfPages = $this->getNumberOfPages($numberOfRecords, $recordsPerPage);

        return [
            'numberOfRecords' => $numberOfRecords,
            'numberOfPages' => $numberOfPages,
            'records' => $recordsResult
        ];
    }

    /**
     * @param array $parameters
     * @return DqlPaginator
     */
    public function setParameters(array $parameters): DqlPaginator
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * @return array
     */
    public function getSql(): array
    {
        return $this->sql;
    }

    /**
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getNumberOfRecordsResult(): int
    {
        $numberOfRecordsQuery = $this->entityManager->createQuery($this->numberOfRecordsDql);
        if(!empty($this->parameters)) {
            $numberOfRecordsQuery->setParameters($this->parameters);
        }
        if(true === $this->debugSql) {
            $this->sql[] = $numberOfRecordsQuery->getSQL();
        }
        return $numberOfRecordsQuery->getSingleScalarResult();
    }

    /**
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @return array
     */
    private function getSurrogateIdsResult(int $pageNumber, int $recordsPerPage): array
    {
        $surrogateIdsOffset = $this->getOffset($pageNumber, $recordsPerPage);
        $surrogateIdsQuery = $this->entityManager->createQuery($this->surrogateIdsDql)->setFirstResult($surrogateIdsOffset)->setMaxResults($recordsPerPage);
        if(!empty($this->parameters)) {
            $surrogateIdsQuery->setParameters($this->parameters);
        }
        if(true === $this->debugSql) {
            $this->sql[] = $surrogateIdsQuery->getSQL();
        }

        $result = $surrogateIdsQuery->getScalarResult();
        if (is_array($result) && count($result) > 0) {
            $column = array_keys($result[0])[0];
            $result = array_column($result, $column);
        }

        return $result;
    }

    /**
     * @param array $surrogateIdsResult
     * @return array
     */
    private function getRecordsResult(array $surrogateIdsResult): array
    {
        $surrogateIdsForWhereIn = $this->getSurrogateIdsForWhereInDql($surrogateIdsResult);
        $recordsQuery = $this->entityManager->createQuery(str_replace(':surrogateIds', $surrogateIdsForWhereIn, $this->recordsDql));
        if(true === $this->debugSql) {
            $this->sql[] = $recordsQuery->getSQL();
        }
        return $recordsQuery->getScalarResult();
    }

    /**
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @return int
     */
    private function getOffset(int $pageNumber, int $recordsPerPage): int
    {
        $offset = ($pageNumber - 1) * $recordsPerPage;
        if($offset < 0) {
            $offset = 0;
        }

        return $offset;
    }

    /**
     * @param array $surrogateIdsResult
     * @return string
     */
    private function getSurrogateIdsForWhereInDql(array $surrogateIdsResult): string
    {
        $surrogateIds = [];
        array_walk_recursive($surrogateIdsResult, function ($item) use (&$surrogateIds) {
            $surrogateIds[] = $item;
        });

        return implode(',', $surrogateIds);
    }

    /**
     * @param int $numberOfRecords
     * @param int $recordsPerPage
     * @return int
     */
    private function getNumberOfPages(int $numberOfRecords, int $recordsPerPage): int
    {
        return intval(ceil($numberOfRecords / $recordsPerPage));
    }
}