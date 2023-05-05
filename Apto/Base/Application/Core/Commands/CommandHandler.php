<?php

namespace Apto\Base\Application\Core\Commands;

use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoTranslatedValueItem;
use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFile;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFileRepository;

abstract class CommandHandler
{
    /**
     * @param array $value
     * @return AptoTranslatedValue
     */
    protected function getTranslatedValue(array $value): AptoTranslatedValue
    {
        $translations = [];
        foreach ($value as $isoname => $translation) {
            $isocode = new AptoLocale($isoname);
            $translations[$isocode->getName()] = new AptoTranslatedValueItem(
                $isocode,
                $translation
            );
        }

        return new AptoTranslatedValue($translations);
    }

    /**
     * @param string $path
     * @param MediaFileRepository $repository
     * @param MediaFileSystemConnector $connector
     * @return MediaFile
     */
    protected function getMediaFileFromPath(string $path, MediaFileRepository $repository, MediaFileSystemConnector $connector): MediaFile
    {
        $file = File::createFromPath($path);
        $mediaFile = $repository->findOneByFile($file);

        // create new MediaFile if none found
        if (null === $mediaFile) {

            // assert that file exists
            if (!$connector->existsFile($file)) {
                throw new \InvalidArgumentException('The file \'' . $file->getPath() . '\' does not exist.');
            }

            // create MediaFile
            $mediaFile = new MediaFile(
                $repository->nextIdentity(),
                $file
            );
            $mediaFile
                ->setMd5Hash($connector->getFileMd5Hash($file))
                ->setSize($connector->getFileSize($file));

            // persist model
            $repository->add($mediaFile);
        }

        return $mediaFile;
    }
}