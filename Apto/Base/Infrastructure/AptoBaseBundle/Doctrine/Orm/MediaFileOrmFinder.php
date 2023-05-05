<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Application\Core\Query\MediaFile\MediaFileFinder;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFile;

class MediaFileOrmFinder extends AptoOrmFinder implements MediaFileFinder
{
    const ENTITY_CLASS = MediaFile::class;

    /**
     * @param string $id
     * @return array|null
     */
    public function findById(string $id)
    {
        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findById($id)
            ->setValues([
                'm' => [
                    ['id.id', 'id'],
                    ['file.directory.path', 'directory'],
                    ['file.filename', 'filename'],
                    ['file.extension', 'extension'],
                    'created',
                    'size',
                    'md5Hash'
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

    /**
     * @param string $file
     * @return array|null
     */
    public function findByFile(string $file)
    {
        $file = File::createFromPath($file);
        $directory = rtrim($file->getDirectory()->getPath(), '/');

        $builder = new DqlQueryBuilder($this->entityClass);
        $builder
            ->findByProperty('file.directory.path', $directory)
            ->findByProperty('file.filename', $file->getFilename())
            ->findByProperty('file.extension', $file->getExtension())
            ->setValues([
                'm' => [
                    ['id.id', 'id'],
                    ['file.directory.path', 'directory'],
                    ['file.filename', 'filename'],
                    ['file.extension', 'extension'],
                    'created',
                    'size',
                    'md5Hash'
                ]
            ]);

        return $builder->getSingleResultOrNull($this->entityManager);
    }

}