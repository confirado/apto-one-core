<?php

namespace Apto\Plugins\ImageUpload\Application\Core\Query;

use Apto\Base\Application\Core\Query\AptoFinder;
use Apto\Base\Domain\Core\Model\AptoUuid;

interface CanvasFinder extends AptoFinder
{
    /**
     * @param AptoUuid $id
     * @return array|null
     */
    public function findById(AptoUuid $id): ?array;

    /**
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @param string $searchString
     * @return array
     */
    public function findList(int $pageNumber = 1, int $recordsPerPage = 20, string $searchString = ''): array;

    /**
     * @return array
     */
    public function findIds(): array;
}
