<?php

namespace Apto\Plugins\PartsList\Application\Core\Service\Converter;

use Money\Currency;
use Apto\Base\Domain\Core\Model\FileSystem\Directory\Directory;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Application\Core\Service\RequestStore;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\DirectoryNotCreatableException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileNotCreatableException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileSystemMountedReadOnlyException;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Application\Core\Query\CustomerGroup\CustomerGroupFinder;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Application\Core\Service\ComputedProductValue\CircularReferenceException;
use Apto\Catalog\Application\Core\Service\Formula\NoProductIdGivenException;
use Apto\Catalog\Application\Core\Service\Formula\NoStateGivenException;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;

class CsvFileConverter
{

    /**
     * @var RequestStore
     */
    private RequestStore $requestStore;

    /**
     * @var MediaFileSystemConnector
     */
    private MediaFileSystemConnector $mediaFileSystemConnector;

    /**
     * @var CsvStringConverter
     */
    private CsvStringConverter $csvStringConverter;

    /**
     * @var CustomerGroupFinder
     */
    private CustomerGroupFinder $customerGroupFinder;

    /**
     * @param RequestStore $requestStore
     * @param MediaFileSystemConnector $mediaFileSystemConnector
     * @param CsvStringConverter $csvStringConverter
     * @param CustomerGroupFinder $customerGroupFinder
     */
    public function __construct(
        RequestStore $requestStore,
        MediaFileSystemConnector $mediaFileSystemConnector,
        CsvStringConverter $csvStringConverter,
        CustomerGroupFinder $customerGroupFinder
    ) {
        $this->requestStore = $requestStore;
        $this->mediaFileSystemConnector = $mediaFileSystemConnector;
        $this->csvStringConverter = $csvStringConverter;
        $this->customerGroupFinder = $customerGroupFinder;
    }

    /**
     * @param AptoUuid $productId
     * @param State $state
     * @param Currency $currency
     * @param AptoUuid $customerGroupId
     * @return string
     * @throws DirectoryNotCreatableException
     * @throws FileNotCreatableException
     * @throws FileSystemMountedReadOnlyException
     * @throws InvalidUuidException
     * @throws CircularReferenceException
     * @throws NoProductIdGivenException
     * @throws NoStateGivenException
     */
    public function convert(AptoUuid $productId, State $state, Currency $currency, AptoUuid $customerGroupId): string
    {
        $customerGroup = $this->customerGroupFinder->findById($customerGroupId->getId());
        $csvString = $this->csvStringConverter->convert($productId, $state, $currency, $customerGroup['shopId'], $customerGroup['externalId']);

        $csvFile = new File($this->generateFolderStructure(), 'stueckliste_' . (new AptoUuid())->getId() . '.csv');
        $this->mediaFileSystemConnector->createFile($csvFile, null, null, true);
        file_put_contents($this->mediaFileSystemConnector->getAbsolutePath($csvFile->getPath()), $csvString);

        return $this->requestStore->getSchemeAndHttpHost() . $this->mediaFileSystemConnector->getFileUrl($csvFile);
    }

    /**
     * @return Directory
     * @throws DirectoryNotCreatableException
     * @throws FileSystemMountedReadOnlyException
     */
    private function generateFolderStructure(): Directory
    {
        $directory = new Directory('/files/partsList/' . date('Y') . '/' . date('m'));

        // create directory if not already exists
        if (!$this->mediaFileSystemConnector->existsDirectory($directory)) {
            $this->mediaFileSystemConnector->createDirectory($directory, true);
        }

        return $directory;
    }
}
