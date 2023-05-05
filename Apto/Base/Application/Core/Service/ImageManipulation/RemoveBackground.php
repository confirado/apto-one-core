<?php

namespace Apto\Base\Application\Core\Service\ImageManipulation;

use Apto\Base\Domain\Core\Model\FileSystem\File\File;

interface RemoveBackground
{
    /**
     * @param File $srcFile
     * @param File $destFile
     */
    public function removeBackground(File $srcFile, File $destFile);
}