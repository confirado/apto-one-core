<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Doctrine\Orm;

use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFile;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFileRepository;

class MediaFileOrmRepository extends AptoOrmRepository implements MediaFileRepository
{
    const ENTITY_CLASS = MediaFile::class;

    /**
     * @param MediaFile $model
     */
    public function update(MediaFile $model)
    {
        /** @phpstan-ignore-next-line */
        $this->_em->merge($model);
    }

    /**
     * @param MediaFile $model
     */
    public function add(MediaFile $model)
    {
        $this->_em->persist($model);
    }

    /**
     * @param MediaFile $model
     */
    public function remove(MediaFile $model)
    {
        $this->_em->remove($model);
    }

    /**
     * @param int $surrogateId
     * @return MediaFile|null
     */
    public function findById($surrogateId)
    {
        $builder = $this->createQueryBuilder('MediaFile')
            ->where('MediaFile.surrogate_id = :surrogateId')
            ->setParameter('surrogateId', $surrogateId);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $directory
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByDirectory(string $directory)
    {
        $builder = $this->createQueryBuilder('MediaFile')
            ->where('MediaFile.file.directory.path LIKE :directory')
            ->setParameter('directory', $directory . '%');

        return $builder->getQuery()->getResult();
    }

    /**
     * @param File $file
     * @return MediaFile|null
     */
    public function findOneByFile(File $file)
    {
        $directory = rtrim($file->getDirectory()->getPath(), '/');

        $builder = $this->createQueryBuilder('MediaFile')
            ->where('MediaFile.file.directory.path = :directory')
            ->andWhere('MediaFile.file.filename = :filename')
            ->andWhere('MediaFile.file.extension = :extension')
            ->setParameter('directory', $directory)
            ->setParameter('filename', $file->getFilename())
            ->setParameter('extension', $file->getExtension());

        return $builder->getQuery()->getOneOrNullResult();
    }
}
