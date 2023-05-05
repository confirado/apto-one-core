<?php
namespace Apto\Base\Domain\Core\Model\MediaFile;

use Apto\Base\Domain\Core\Model\AptoRepository;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;

interface MediaFileRepository extends AptoRepository
{
    /**
     * @param MediaFile $model
     */
    public function update(MediaFile $model);

    /**
     * @param MediaFile $model
     */
    public function add(MediaFile $model);

    /**
     * @param MediaFile $model
     */
    public function remove(MediaFile $model);

    /**
     * @param $id
     * @return MediaFile|null
     */
    public function findById($id);

    /**
     * @param string $directory
     * @return mixed
     */
    public function findByDirectory(string $directory);

    /**
     * @param File $file
     * @return MediaFile|null
     */
    public function findOneByFile(File $file);
}