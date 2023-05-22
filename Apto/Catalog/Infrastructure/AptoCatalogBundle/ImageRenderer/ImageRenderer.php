<?php

namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\ImageRenderer;

use Apto\Base\Domain\Core\Model\FileSystem\Directory\Directory;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\DirectoryNotCreatableException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileNotCreatableException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileNotRemovableException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FilePermissionSetFailedException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileSystemMountedReadOnlyException;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\FileSystemConnector;
use Apto\Base\Domain\Core\Model\FileSystem\CacheFileSystemConnector;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Model\FileSystem\RootFileSystemConnector;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Core\Service\AptoJsonSerializer;
use Apto\Base\Domain\Core\Service\AptoJsonSerializerException;
use Apto\Base\Domain\Core\Service\AptoParameterInterface;
use Apto\Base\Infrastructure\AptoBaseBundle\FileSystem\LocalFileSystem;
use Apto\Catalog\Application\Core\Query\Product\Element\ImageRenderer as ImageRendererInterface;
use Apto\Catalog\Application\Core\Query\Product\Element\ProductElementFinder;
use Apto\Catalog\Application\Core\Service\RenderImage\RenderImageProvider;
use Apto\Catalog\Application\Core\Service\RenderImage\RenderImageReducer;
use Apto\Catalog\Application\Core\Service\RenderImage\RenderImageRegistry;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Element\RenderImage;
use Exception;
use InvalidArgumentException;

class ImageRenderer implements ImageRendererInterface
{
    use FormulaParser;

    const RENDERED_IMAGE_EXTENSION = 'png';
    const RENDERED_IMAGE_QUALITY = '90';
    const RENDERED_IMAGE_THUMB_EXTENSION = 'jpg';
    const RENDERED_IMAGE_THUMB_QUALITY = '60';


    /**
     * @var RootFileSystemConnector
     */
    protected $localFilesystem;

    /**
     * @var MediaFileSystemConnector
     */
    protected $srcFilesystem;

    /**
     * @var CacheFileSystemConnector
     */
    protected $dstFilesystem;

    /**
     * @var RenderImageRegistry
     */
    protected $renderImageRegistry;

    /**
     * @var string
     */
    protected $convertPath;

    /**
     * @var string
     */
    protected $convertPathThumb;

    /**
     * @var bool
     */
    protected $useImageCache;

    /**
     * @param ProductElementFinder $productElementFinder
     * @param AptoJsonSerializer $aptoJsonSerializer
     * @param RootFileSystemConnector $localFilesystem
     * @param MediaFileSystemConnector $srcFilesystem
     * @param CacheFileSystemConnector $dstFilesystem
     * @param RenderImageRegistry $renderImageRegistry
     * @param AptoParameterInterface $aptoParameter
     */
    public function __construct(
        ProductElementFinder $productElementFinder,
        AptoJsonSerializer $aptoJsonSerializer,
        RootFileSystemConnector $localFilesystem,
        MediaFileSystemConnector $srcFilesystem,
        CacheFileSystemConnector $dstFilesystem,
        RenderImageRegistry $renderImageRegistry,
        AptoParameterInterface $aptoParameter
    ) {
        $this->productElementFinder = $productElementFinder;
        $this->aptoJsonSerializer  = $aptoJsonSerializer;
        $this->localFilesystem = $localFilesystem;
        $this->srcFilesystem = $srcFilesystem;
        $this->dstFilesystem = $dstFilesystem;
        $this->renderImageRegistry = $renderImageRegistry;
        $this->convertPath = $aptoParameter->get('image_magick_path');
        $this->convertPathThumb = $aptoParameter->get('graphics_magick_path');
        $this->useImageCache = $aptoParameter->get('image_renderer_use_cache');
    }

    /**
     * @param string $perspective
     * @param State $state
     * @param string|null $productId
     * @return array
     */
    protected function getProviderImageList(string $perspective, State $state, string $productId = null): array
    {
        $renderImageProviders = $this->renderImageRegistry->getRenderImageProviders();
        $providerImageList = [];

        /** @var RenderImageProvider $renderImageProvider */
        foreach ($renderImageProviders as $renderImageProvider) {
            $providerImageList = array_merge($providerImageList, $renderImageProvider->getRenderImageList($perspective, $state, $productId));
        }

        return $providerImageList;
    }

    /**
     * @param array $imageList
     * @return array
     */
    public function sortImageListByLayer(array $imageList): array
    {
        // sort by layer
        usort($imageList, function (array $a, array $b) {
            if ($a['layer'] === $b['layer']) {
                return 0;
            }

            return ($a['layer'] < $b['layer']) ? -1 : 1;
        });

        return $imageList;
    }

    /**
     * @param array $imageList
     * @param string $perspective
     * @param State $state
     * @param string|null $productId
     * @return array
     */
    protected function getReducerImageList(array $imageList, string $perspective, State $state, string $productId = null): array
    {
        $renderImageReducers = $this->renderImageRegistry->getRenderImageReducers();

        /** @var RenderImageReducer $renderImageReducer */
        foreach ($renderImageReducers as $renderImageReducer) {
            $imageList = $renderImageReducer->getRenderImageList($perspective, $state, $imageList, $productId);
        }

        return $imageList;
    }

    /**
     * @param State $state
     * @param array $imageList
     * @param string $perspective
     * @param bool $createThumb
     * @param bool $returnUrl
     * @return string
     * @throws AptoJsonSerializerException
     * @throws DirectoryNotCreatableException
     * @throws FileNotCreatableException
     * @throws FileNotRemovableException
     * @throws FilePermissionSetFailedException
     * @throws FileSystemMountedReadOnlyException
     * @throws InvalidUuidException
     */
    public function getRenderImageByImageList(State $state, array $imageList, string $perspective, bool $createThumb = false, bool $returnUrl = true): string
    {
        $renderImageFile = $this->getRenderImageFileByImageList($state, $imageList, $perspective, $createThumb);
        if (true === $returnUrl) {
            return $this->dstFilesystem->getFileUrl($renderImageFile);
        }

        return $this->dstFilesystem->getAbsolutePath($renderImageFile->getPath());
    }

    /**
     * @param State $state
     * @param array $imageList
     * @param string $perspective
     * @param bool $createThumb
     * @return File
     * @throws AptoJsonSerializerException
     * @throws DirectoryNotCreatableException
     * @throws FileNotCreatableException
     * @throws FileNotRemovableException
     * @throws FilePermissionSetFailedException
     * @throws FileSystemMountedReadOnlyException
     * @throws InvalidUuidException
     */
    public function getRenderImageFileByImageList(State $state, array $imageList, string $perspective, bool $createThumb = false): File
    {
        $renderedDstFile = $this->getRenderedDstFile($state, $imageList, $perspective, $createThumb);
        if ($this->dstFilesystem->existsFile($renderedDstFile) && $this->useImageCache === true){
            return $renderedDstFile;
        }

        $srcIsLocalFileSystem = $this->srcFilesystem instanceof LocalFileSystem;
        $dstIsLocalFileSystem = $this->dstFilesystem instanceof LocalFileSystem;
        $srcFiles = $this->getSrcFiles($imageList, $srcIsLocalFileSystem);
        $srcFilesystem =& $this->srcFilesystem;
        $dstFilesystem =& $this->dstFilesystem;

        if (!$srcIsLocalFileSystem) {
            $srcFilesystem =& $this->localFilesystem;
        }

        if (!$dstIsLocalFileSystem) {
            $dstFilesystem =& $this->localFilesystem;
            $dstFile = $this->allocateLocalTemporaryFile('dst');
        } else {
            $dstFile =& $renderedDstFile;
        }

        $this->createRenderedImage($state, $srcFilesystem, $dstFilesystem, $srcFiles, $dstFile, $createThumb, $imageList);

        if (!$srcIsLocalFileSystem) {
            $this->removeLocalTemporaryFiles($srcFiles);
        }

        if (!$dstIsLocalFileSystem) {
            $this->dstFilesystem->createFile($renderedDstFile, $dstFile, $this->localFilesystem, true);
            $this->localFilesystem->removeFile($dstFile);
        }

        return $renderedDstFile;
    }

    /**
     * @param array $imageList
     * @param string $perspective
     * @param State $state
     * @param bool $createThumb
     * @param bool $returnUrl
     * @param string|null $productId
     * @return string
     * @throws AptoJsonSerializerException
     * @throws DirectoryNotCreatableException
     * @throws FileNotCreatableException
     * @throws FileNotRemovableException
     * @throws FilePermissionSetFailedException
     * @throws FileSystemMountedReadOnlyException
     * @throws InvalidUuidException
     */
    public function getImageByImageList(array $imageList, string $perspective, State $state, bool $createThumb = false, bool $returnUrl = true, string $productId = null): string
    {
        $imageList = $this->getProviderAndReducerImages($imageList, $perspective, $state, $productId);

        if (empty($imageList)) {
            return '';
        }

        $imageList = $this->sortImageListByLayer($imageList);

        return $this->getRenderImageByImageList($state, $imageList, $perspective, $createThumb, $returnUrl);
    }

    /**
     * @param array $imageList
     * @param string $perspective
     * @param State $state
     * @param bool $createThumb
     * @param string|null $productId
     * @return File|null
     * @throws AptoJsonSerializerException
     * @throws DirectoryNotCreatableException
     * @throws FileNotCreatableException
     * @throws FileNotRemovableException
     * @throws FilePermissionSetFailedException
     * @throws FileSystemMountedReadOnlyException
     * @throws InvalidUuidException
     */
    public function getImageFileByImageList(array $imageList, string $perspective, State $state, bool $createThumb = false, string $productId = null): ?File
    {
        $imageList = $this->getProviderAndReducerImages($imageList, $perspective, $state, $productId);

        if (empty($imageList)) {
            return null;
        }

        $imageList = $this->sortImageListByLayer($imageList);

        return $this->getRenderImageFileByImageList($state, $imageList, $perspective, $createThumb);
    }

    /**
     * @param array $imageList
     * @param string $perspective
     * @param State $state
     * @param $productId
     * @return bool
     */
    public function hasStatePerspectiveImage(array $imageList, string $perspective, State $state, $productId): bool
    {
        $imageList = $this->getProviderAndReducerImages($imageList, $perspective, $state, $productId);

        if (empty($imageList)) {
            return false;
        }

        return true;
    }

    /**
     * @param array $imageList
     * @param string $perspective
     * @param State $state
     * @param string|null $productId
     * @return array
     */
    public function getProviderAndReducerImages(array $imageList, string $perspective, State $state, string $productId = null): array
    {
        $providerImageList = $this->getProviderImageList($perspective, $state, $productId);

        // merge default list with provider and reducer lists
        $imageList = array_merge($imageList, $providerImageList);
        return $this->getReducerImageList($imageList, $perspective, $state, $productId);
    }

    /**
     * Get a list of all src images, copy non-local images to local filesystem
     * @param array $imageList
     * @param bool $srcIsLocalFileSystem
     * @return File[]
     * @throws FileNotCreatableException
     * @throws FilePermissionSetFailedException
     * @throws FileSystemMountedReadOnlyException
     */
    protected function getSrcFiles(array $imageList, bool $srcIsLocalFileSystem): array
    {
        $srcFiles = [];

        foreach ($imageList as $image) {
            $srcDirectory = new Directory($image['path']);
            $srcFile = new File($srcDirectory, $image['filename'] . '.' . $image['extension']);

            if ($srcIsLocalFileSystem) {
                // use local file
                $srcFiles[] = $srcFile;
            } else {
                // copy remote file to local filesystem
                $tmpSrcFile = $this->allocateLocalTemporaryFile('src');
                $this->localFilesystem->createFile($tmpSrcFile, $srcFile, $this->srcFilesystem, true);
                $this->localFilesystem->setFilePermission($tmpSrcFile, 0700);
                // use new local copy of remote file
                $srcFiles[] = $tmpSrcFile;
            }
        }

        return $srcFiles;
    }

    /**
     * @param State $state
     * @param FileSystemConnector $srcFileSystem
     * @param FileSystemConnector $dstFileSystem
     * @param array $srcFiles
     * @param File $dstFile
     * @param bool $createThumb
     * @param array $imageList
     * @throws Exception
     */
    protected function createRenderedImage(State $state, FileSystemConnector $srcFileSystem, FileSystemConnector $dstFileSystem, array $srcFiles, File $dstFile, bool $createThumb = false, array $imageList = [])
    {
        $convertPath = $this->convertPath;
        $renderedImageExtension = self::RENDERED_IMAGE_EXTENSION;
        $renderedImageQuality = self::RENDERED_IMAGE_QUALITY;
        $dimensions = null;

        if (true === $createThumb) {
            $convertPath = $this->convertPathThumb;
            $renderedImageExtension = self::RENDERED_IMAGE_THUMB_EXTENSION;
            $renderedImageQuality = self::RENDERED_IMAGE_THUMB_QUALITY;
        }

        $convertString = $convertPath . ' -quality ' . $renderedImageQuality;

        $outputOptions = ' -background none -compose Over -layers flatten ';

        if (strtolower($renderedImageExtension) !== 'png') {
            $outputOptions = ' -layers flatten ';
        }

        if (strpos($convertPath, 'convert gm') !== false) {
            $renderedImageExtension = 'jpg';
            $outputOptions = ' -flatten ';
        }

        foreach ($srcFiles as $index => $srcFile) {
            /** @var File $srcFile */
            if (!$srcFileSystem->existsFile($srcFile)) {
                throw new Exception('Cant render image because a required render image has not been found.');
            }

            if (null === $dimensions) {
                $dimensions = getimagesize($srcFileSystem->getAbsolutePath($srcFile->getPath()));
            }

            // resize strategy | runtime ca 400ms with gm jpg
            /*if ($index === 0) {
                $convertString .= ' -page +0+0 -resize 1024x1024 ' . escapeshellarg($srcFileSystem->getAbsolutePath($srcFile->getPath()));
            } else {
                $convertString .= ' -page +512+512 -resize 512x512 ' . escapeshellarg($srcFileSystem->getAbsolutePath($srcFile->getPath()));
            }*/

            // position strategy (background double sized)  | runtime ca 350ms with gm jpg
            /*if ($index === 0) {
                $convertString .= ' -page +0+0 ' . escapeshellarg($srcFileSystem->getAbsolutePath($srcFile->getPath()));
            } else {
                $convertString .= ' -page +512+512 ' . escapeshellarg($srcFileSystem->getAbsolutePath($srcFile->getPath()));
            }*/

            $srcFilePath = escapeshellarg($srcFileSystem->getAbsolutePath($srcFile->getPath()));
            $renderImageOptions = null;
            $currentImage = $imageList[$index];

            if (array_key_exists('renderImageOptions', $currentImage)) {
                $renderImageOptions = $currentImage['renderImageOptions'];
            }

            // replace src file with repeated version, if requested
            if ($renderImageOptions && $renderImageOptions['renderImageOptions']['type'] === 'Wiederholbar') {
                $renderImageDimensions = $this->getRepeatableRenderImageDimensions($state, $renderImageOptions);
                $srcFile = $this->getRepeatableRenderImage($currentImage['renderImageId'], $renderImageOptions['renderImageOptions'], $renderImageDimensions, $srcFileSystem, $dstFileSystem, $dstFile->getDirectory(), $dimensions, $srcFile);
                if ($srcFile === null) {
                    continue;
                }
                $srcFilePath = escapeshellarg($dstFileSystem->getAbsolutePath($srcFile->getPath()));
            }

            // place image at offset
            if ($renderImageOptions && $renderImageOptions['offsetOptions']['type'] === 'Berechnend') {
                $offset = $this->getCalculatedOffset($state, $renderImageOptions);
            } else if ($currentImage['offsetX'] != 0 || $currentImage['offsetY'] != 0 ) {
                $offset = $this->getStaticOffset($currentImage, $dimensions);
            } else {
                // all same size strategy (1024x1024) | runtime ca 230ms with gm jpg
                $offset = ['x' => 0, 'y' => 0];
            }
            $convertString .= ' -page +' . $offset['x'] . '+' . $offset['y'] . ' ' . $srcFilePath;
        }

        // add output options and destination format/file
        $convertString .= $outputOptions . escapeshellarg($renderedImageExtension . ':' . $dstFileSystem->getAbsolutePath($dstFile->getPath()));

        // execute convert command
        exec($convertString);

        // assert existing output file
        if (!$dstFileSystem->existsFile($dstFile)) {
            throw new Exception(sprintf(
                'Cant create rendered image "%s".',
                $dstFile->getFilename() . '.' . $dstFile->getExtension()
            ));
        }
    }

    /**
     * Calculate offset depending on selected unit and given size
     * @param float $offset
     * @param int $unit
     * @param int $size
     * @return float
     */
    protected static function calculateOffset(float $offset, int $unit, int $size): float
    {
        switch ($unit) {

            // use relative offset in percent relative to size
            case RenderImage::OFFSET_UNIT_PERCENT:
                return $size * $offset / 100;

            // use absolute offset in pixel
            case RenderImage::OFFSET_UNIT_PIXEL:
                return floor($offset);

            // invalid offset unit detected
            default:
                throw new InvalidArgumentException(sprintf(
                    'The given value "%s" is not within the valid offset unit types (%s).',
                    $unit,
                    implode(', ', RenderImage::VALID_OFFSET_UNITS)
                ));
        }
    }

    /**
     * @param string $renderImageId
     * @param array $renderImageOptions
     * @param array $renderImageDim
     * @param FileSystemConnector $srcFileSystem
     * @param FileSystemConnector $dstFileSystem
     * @param Directory $dstFileDirectory
     * @param array $dimensions
     * @param File $srcFile
     * @return File|null
     */
    protected function getRepeatableRenderImage(string $renderImageId, array $renderImageOptions, array $renderImageDim, FileSystemConnector $srcFileSystem, FileSystemConnector $dstFileSystem, Directory $dstFileDirectory, array $dimensions, File $srcFile): ?File
    {
        $dstFile = new File($dstFileDirectory, $renderImageId . '.' . self::RENDERED_IMAGE_EXTENSION);
        $srcFilePath = escapeshellarg($srcFileSystem->getAbsolutePath($srcFile->getPath()));
        $dstFilePath = escapeshellarg($dstFileSystem->getAbsolutePath($dstFile->getPath()));

        if ($renderImageDim['width'] === '' && $renderImageOptions['formulaHorizontal']) {
            $renderImageDim['width'] = $dimensions[0];
        }

        if ($renderImageDim['height'] === '' && $renderImageOptions['formulaVertical']) {
            $renderImageDim['height'] = $dimensions[1];
        }

        if ($renderImageDim['width'] === '' || $renderImageDim['height'] === '') {
            return null;
        }

        $convertString = $this->convertPath . ' -size ' . $renderImageDim['width'] . 'x' . $renderImageDim['height'] . ' -colorspace sRGB -channel rgba -alpha on -background none tile:' . $srcFilePath . ' ' .$dstFilePath;

        exec($convertString);

        return $dstFile;
    }

    /**
     * @param State $state
     * @param array|null $renderImageOptions
     * @return array
     * @throws AptoJsonSerializerException
     * @throws InvalidUuidException
     */
    protected function getRepeatableRenderImageDimensions(State $state, ?array $renderImageOptions): array
    {
        $result = [
            'width' => '',
            'height' => ''
        ];

        if (!$renderImageOptions) {
            return $result;
        }

        $renderImageOptions = $renderImageOptions['renderImageOptions'];

        // get formula aliases
        $formulaAliases = $this->getFormulaAliases($state, $renderImageOptions);

        // calculate width and height
        if (null !== ($formulaResult = self::calculateFormula($renderImageOptions['formulaHorizontal'], $formulaAliases))) {
            $result['width'] = $formulaResult;
        }
        if (null !== ($formulaResult = self::calculateFormula($renderImageOptions['formulaVertical'], $formulaAliases))) {
            $result['height'] = $formulaResult;
        }

        return $result;
    }

    /**
     * @param State $state
     * @param array $imageList
     * @param string $perspective
     * @param bool $createThumb
     * @return File
     * @throws AptoJsonSerializerException
     * @throws FileSystemMountedReadOnlyException
     * @throws InvalidUuidException
     * @throws DirectoryNotCreatableException
     */
    protected function getRenderedDstFile(State $state, array $imageList, string $perspective, bool $createThumb = false): File
    {
        $imageFileName = '';
        $imageDirectory = '/rendered/';
        $renderedImageExtension = self::RENDERED_IMAGE_EXTENSION;

        if (strpos($this->convertPath, 'convert gm') !== false) {
            $renderedImageExtension = 'jpg';
        }

        if (true === $createThumb) {
            $renderedImageExtension = self::RENDERED_IMAGE_THUMB_EXTENSION;
            $imageDirectory = '/thumbs/';
        }

        foreach ($imageList as $index => $element) {
            $renderedImageOptions = null;
            if (array_key_exists('renderImageOptions', $element)) {
                $renderedImageOptions = $element['renderImageOptions'];
            }

            if ($index === 0) {
                $imageDirectory .= $element['productId'];
                $imageFileName .= $element['renderImageId'];
            } else {
                $imageFileName .= '-' . $element['renderImageId'];
            }

            $renderImageDim = $this->getRepeatableRenderImageDimensions($state, $renderedImageOptions);
            $imageFileName .= '-' . $renderImageDim['width'] . '-' . $renderImageDim['height'];

            $offsetDim = $this->getCalculatedOffset($state, $renderedImageOptions);
            $imageFileName .= '-' . $offsetDim['x'] . '-' . $offsetDim['y'];
        }

        $imageFileName = sha1($imageFileName) . '.' . $renderedImageExtension;
        $imageDirectory .= '/' . $perspective . '/' . substr($imageFileName, 0, 2) . '/' . substr($imageFileName, 2, 2) . '/';

        $directory = new Directory($imageDirectory);
        $file = new File($directory, $imageFileName);

        // create destination directory, if no existing
        if (!$this->dstFilesystem->existsDirectory($file->getDirectory())) {
            $this->dstFilesystem->createDirectory($file->getDirectory(), true);
        }

        return $file;
    }

    /**
     * Get zero offset
     * @return array
     */
    protected function getZeroOffset(): array
    {
        return [
            'x' => 0,
            'y' => 0
        ];
    }

    /**
     * Get static offset
     * @param array $currentImage
     * @param array $dimensions
     * @return array
     */
    protected function getStaticOffset(array $currentImage, array $dimensions): array
    {
        return [
            'x' => self::calculateOffset(
                $currentImage['offsetX'],
                $currentImage['renderImageOptions']['offsetOptions']['offsetUnitX'] ?? RenderImage::OFFSET_UNIT_PERCENT,
                $dimensions[0]
            ),
            'y' => self::calculateOffset(
                $currentImage['offsetY'],
                $currentImage['renderImageOptions']['offsetOptions']['offsetUnitY'] ?? RenderImage::OFFSET_UNIT_PERCENT,
                $dimensions[1]
            )
        ];
    }

    /**
     * Get calculated offset
     * @param State $state
     * @param array|null $renderImageOptions
     * @return array
     * @throws AptoJsonSerializerException
     * @throws InvalidUuidException
     */
    protected function getCalculatedOffset(State $state, ?array $renderImageOptions): array
    {
        $offset = $this->getZeroOffset();

        if (!$renderImageOptions) {
            return $offset;
        }

        $offsetOptions = $renderImageOptions['offsetOptions'];

        // get formula aliases
        $formulaAliases = $this->getFormulaAliases($state, $offsetOptions);

        // calculate x and y offset
        if (null !== ($formulaResult = self::calculateFormula($offsetOptions['formulaOffsetX'], $formulaAliases))) {
            $offset['x'] = $formulaResult;
        }
        if (null !== ($formulaResult = self::calculateFormula($offsetOptions['formulaOffsetY'], $formulaAliases))) {
            $offset['y'] = $formulaResult;
        }

        return $offset;
    }

    /**
     * @param string $prefix
     * @return File
     */
    protected function allocateLocalTemporaryFile(string $prefix = ''): File
    {
        // hint: windows uses the first 3 chars only...
        $path = tempnam(sys_get_temp_dir(), 'apto_cache_' . $prefix . '_');

        return File::createFromPath($path);
    }

    /**
     * @param array $tmpFiles
     * @throws FileNotRemovableException
     * @throws FileSystemMountedReadOnlyException
     */
    protected function removeLocalTemporaryFiles(array $tmpFiles)
    {
        foreach ($tmpFiles as $tmpFile) {
            /** @var File $tmpFile */
            $this->localFilesystem->removeFile($tmpFile);
        }
    }
}
