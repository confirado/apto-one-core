<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Controller;

use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Infrastructure\AptoBaseBundle\Thumbnail\ImageMagickThumbnailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ThumbnailController extends AbstractController
{
    /**
     * @Route("/{filename}", defaults={"filename"=""}, requirements={"filename"=".+\..+_[0-9]*x[0-9]*\.(jpg|png|gif)"})
     * @param Request $request
     * @param MediaFileSystemConnector $mediaFileSystem
     * @param ImageMagickThumbnailService $thumbnailService
     * @return Response
     */
    public function getThumbnailAction(Request $request, MediaFileSystemConnector $mediaFileSystem, ImageMagickThumbnailService $thumbnailService): Response
    {
        // parse url
        $matches = [];
        if (1 != preg_match('/(.+\..+)_([0-9]*)x([0-9]*)\.(jpg|png|gif)/', $request->get('filename'), $matches)) {
            throw new NotFoundHttpException();
        }
        $originalFilename = $matches[1];
        $width = $matches[2];
        $height = $matches[3];
        $thumbnailFormat = $matches[4];

        // exit if both width and height are empty
        if ('' == $width && '' == $height) {
            throw new NotFoundHttpException();
        }

        // exit with 404, if file does not exist
        $mediaFile = File::createFromPath($originalFilename);
        if (!$mediaFileSystem->existsFile($mediaFile)) {
            throw new NotFoundHttpException();
        }

        // create thumbnail
        $url = $thumbnailService->getThumbnailUrl(
            $mediaFile,
            $mediaFileSystem,
            $width,
            $height,
            $thumbnailService::MODE_SHRINK,
            $thumbnailFormat
        );

        // do redirect, let apache deliver image file
        return $this->redirect($url);
    }
}