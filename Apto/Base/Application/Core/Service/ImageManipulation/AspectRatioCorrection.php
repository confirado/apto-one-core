<?php

namespace Apto\Base\Application\Core\Service\ImageManipulation;

use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\RootReadOnlyFileSystemConnector;

class AspectRatioCorrection
{
    /**
     * @var RootReadOnlyFileSystemConnector
     */
    private $readOnlyFileSystemConnector;

    /**
     * @var string
     */
    private $convertPath;

    /**
     * @param RootReadOnlyFileSystemConnector $readOnlyFileSystemConnector
     * @param string $convertPath
     */
    public function __construct(RootReadOnlyFileSystemConnector $readOnlyFileSystemConnector, string $convertPath)
    {
        $this->readOnlyFileSystemConnector = $readOnlyFileSystemConnector;
        $this->convertPath = $convertPath;
    }

    /**
     * @param File $image
     * @param int $aspectWidth
     * @param int $aspectHeight
     */
    public function correctAspectRation(File $image, int $aspectWidth, int $aspectHeight)
    {
        $absolutePath = $this->readOnlyFileSystemConnector->getAbsolutePath($image->getPath());
        list($imageWidth, $imageHeight) = getimagesize($absolutePath);
        $imageRation = $imageWidth / $imageHeight;
        $aspectRation = $aspectWidth / $aspectHeight;

        if ($aspectRation <= $imageRation) {
            // bild muss höhe werden
            $imageHeight = $imageWidth * $aspectRation;
        } else {
            // bild muss breiter werden
            $imageWidth = $imageHeight * $aspectRation;
        }

        $command = $this->convertPath . ' ' . 'magick convert ' . $absolutePath . '  -background none -gravity center -extent ' . $imageWidth . 'x' . $imageHeight . ' ' . $absolutePath;
        exec($command);
    }
}
