<?php

namespace Apto\Plugins\ImportExport\Application\Backend\Commands\Import\MaterialPicker;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Base\Domain\Core\Model\AptoLocale;
use Apto\Base\Domain\Core\Model\AptoTranslatedValue;
use Apto\Base\Domain\Core\Model\AptoTranslatedValueItem;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\FileSystem\Directory\Directory;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Core\Model\Language\LanguageRepository;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFileRepository;
use Apto\Base\Domain\Core\Service\StringSanitizer;
use Apto\Catalog\Domain\Core\Model\PriceMatrix\PriceMatrix as PriceMatrixEntity;
use Apto\Catalog\Domain\Core\Model\PriceMatrix\PriceMatrixRepository;
use Apto\Catalog\Domain\Core\Model\Product\Identifier;
use Apto\Catalog\Domain\Core\Model\Product\ProductRepository;
use Apto\Catalog\Domain\Core\Model\Shop\ShopRepository;
use Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\Sanitizer\NameSanitizer;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material\Material;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material\MaterialRepository;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Pool\Pool;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Pool\PoolRepository;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\PriceGroup\PriceGroup;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\PriceGroup\PriceGroupRepository;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\PriceGroup\PriceMatrix;

class MaterialCommandHandler extends AbstractCommandHandler
{
    /**
     * @var LanguageRepository
     */
    private $languageRepository;

    /**
     * @var ShopRepository
     */
    private $shopRepository;

    /**
     * @var PoolRepository
     */
    private $poolRepository;

    /**
     * @var PriceGroupRepository
     */
    private $priceGroupRepository;

    /**
     * @var MaterialRepository
     */
    private $materialRepository;

    /**
     * @var MediaFileRepository
     */
    private $mediaFileRepository;

    /**
     * @var MediaFileSystemConnector
     */
    private $mediaFileSystemConnector;

    /**
     * @var PriceMatrixRepository
     */
    private $priceMatrixRepository;

    /**
     * @var StringSanitizer
     */
    protected $sanitizer;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param LanguageRepository       $languageRepository
     * @param ShopRepository           $shopRepository
     * @param PoolRepository           $poolRepository
     * @param PriceGroupRepository     $priceGroupRepository
     * @param MaterialRepository       $materialRepository
     * @param MediaFileRepository      $mediaFileRepository
     * @param MediaFileSystemConnector $mediaFileSystemConnector
     * @param PriceMatrixRepository    $priceMatrixRepository
     * @param StringSanitizer          $sanitizer
     * @param ProductRepository        $productRepository
     */
    public function __construct(
        LanguageRepository $languageRepository,
        ShopRepository $shopRepository,
        PoolRepository $poolRepository,
        PriceGroupRepository $priceGroupRepository,
        MaterialRepository $materialRepository,
        MediaFileRepository $mediaFileRepository,
        MediaFileSystemConnector $mediaFileSystemConnector,
        PriceMatrixRepository $priceMatrixRepository,
        StringSanitizer $sanitizer,
        ProductRepository $productRepository,
    ) {
        $this->languageRepository = $languageRepository;
        $this->shopRepository = $shopRepository;
        $this->poolRepository = $poolRepository;
        $this->priceGroupRepository = $priceGroupRepository;
        $this->materialRepository = $materialRepository;
        $this->mediaFileRepository = $mediaFileRepository;
        $this->mediaFileSystemConnector = $mediaFileSystemConnector;
        $this->priceMatrixRepository = $priceMatrixRepository;
        $this->sanitizer = $sanitizer;
        $this->productRepository = $productRepository;
    }

    /**
     * @param ImportMaterialDataType $command
     * @throws InvalidUuidException
     */
    public function handleImportMaterialDataType(ImportMaterialDataType $command)
    {
        // init fields
        $fields = $command->getFields();

        // init pool
        $pool = $this->getPool(
            $command->getLocale(),
            NameSanitizer::sanitizeName($fields['pool'])
        );

        // init price group
        $priceGroup = $this->getPriceGroup(
            $command->getLocale(),
            $fields['preisgruppe'],
            NameSanitizer::sanitizeName($fields['preisgruppe'])
        );

        // init material
        $materialIdentifier = new Identifier($fields['identifier']);
        $materialId = $this->materialRepository->findFirstIdByIdentifier($materialIdentifier->getValue());

        // add new material if not found
        if (null === $materialId) {
            $material = new Material(
                $this->materialRepository->nextIdentity(),
                $this->getTranslatedValue([$command->getLocale() => $fields['name']])
            );
            $material->setActive(true);

            $this->materialRepository->add($material);
            $material->publishEvents();
        } else {
            $material = $this->materialRepository->findById($materialId);
        }

        // set material properties
        $material
            ->setName(
                AptoTranslatedValue::addTranslation(
                    $material->getName(),
                    new AptoTranslatedValueItem(new AptoLocale($command->getLocale()), $fields['name'])
                )
            )
            ->setIdentifier($materialIdentifier->getValue())
        ;

        if (array_key_exists('beschreibung', $fields)) {
            $material->setDescription(
                AptoTranslatedValue::addTranslation(
                    $material->getDescription(),
                    new AptoTranslatedValueItem(new AptoLocale($command->getLocale()), $fields['beschreibung'])
                )
            );
        }

        if (array_key_exists('reflexion', $fields)) {
            $material->setReflection($this->convertToInt($fields['reflexion']));
        }

        if (array_key_exists('transmission', $fields)) {
            $material->setTransmission($this->convertToInt($fields['transmission']));
        }

        if (array_key_exists('absorption', $fields)) {
            $material->setAbsorption($this->convertToInt($fields['absorption']));
        }

        if (array_key_exists('vorschau-bild', $fields) && '' !== $fields['vorschau-bild']) {
            // sanitize file name
            $directory = dirname($fields['vorschau-bild']);
            $fileName = $this->sanitizer->sanitizeFilename(basename($fields['vorschau-bild']));
            $file = new File(new Directory($directory), $fileName);
            $fields['vorschau-bild'] = $file->getPath();

            // set preview image
            $material->setPreviewImage(
                $this->getMediaFileFromPath(
                    $fields['vorschau-bild'],
                    $this->mediaFileRepository,
                    $this->mediaFileSystemConnector
                )
            );
        }

        foreach ($fields as $key => $value) {
            if (preg_match('/^condition_\d+$/', $key)) {

                $productIdentifier = new Identifier(preg_split("/\|/", $value)[0]);
                $conditionIdentifier = new Identifier(preg_split("/\|/", $value)[1]);

                $product = $this->productRepository->findByIdentifier($productIdentifier);

                foreach($product->getConditionSets() as $conditionSet) {
                    if($conditionSet->getIdentifier()->getValue() === $conditionIdentifier->getValue()) {
                        $material->addConditionSet(new AptoUuid($conditionSet->getId()));
                    }

                    // @todo maybe later create new condition when condition with the given identifier is not found.
                }
            }
        }

        // add material to pool
        $poolItemId = $pool->getItemIdByMaterialId($material->getId());
        if (null !== $poolItemId) {
            $pool->setItemPriceGroup($poolItemId, $priceGroup);
        } else {
            $pool->addItem($material, $priceGroup);
        }

        $this->poolRepository->update($pool);
        $pool->publishEvents();
    }

    /**
     * @param ImportMaterialPriceGroupDataType $command
     * @throws InvalidUuidException
     */
    public function handleImportMaterialPriceGroupDataType(ImportMaterialPriceGroupDataType $command)
    {
        $fields = $command->getFields();
        $locale = $command->getLocale();

        // set public name
        $publicName = $fields['name'];
        if (array_key_exists('public-name', $fields) && trim($fields['public-name']) !== '') {
            $publicName = $fields['public-name'];
        }

        // init price group
        $priceGroup = $this->getPriceGroup(
            $command->getLocale(),
            $publicName,
            NameSanitizer::sanitizeName($fields['name'])
        );

        // set name
        $priceGroup->setName(
            AptoTranslatedValue::addTranslation(
                $priceGroup->getName(),
                new AptoTranslatedValueItem(new AptoLocale($locale), $publicName)
            )
        );
        $priceGroup->setInternalName($this->getTranslatedValue([$locale => $fields['name']]));

        // set surcharge
        if (array_key_exists('surcharge', $fields) && trim($fields['surcharge']) !== '') {
            $priceGroup->setAdditionalCharge(floatval($fields['surcharge']));
        }

        // set price-matrix
        $priceMatrix = [
            'id' => null,
            'row' => null,
            'column' => null,
            'pricePostProcess' => null
        ];

        // set price-matrix id
        if (array_key_exists('price-matrix', $fields) && trim($fields['price-matrix']) !== '') {
            $priceMatrixId = $this->getPriceMatrixId($fields['price-matrix'], $locale);
            if (null !== $priceMatrixId) {
                $priceMatrix['id'] = $priceMatrixId->getId();
            }
        }

        // set row-formula
        if (array_key_exists('row-formula', $fields) && trim($fields['row-formula']) !== '') {
            $priceMatrix['row'] = trim($fields['row-formula']);
        }

        // set column-formula
        if (array_key_exists('column-formula', $fields) && trim($fields['column-formula']) !== '') {
            $priceMatrix['column'] = trim($fields['column-formula']);
        }
        $priceGroup->setPriceMatrix(PriceMatrix::fromArray($priceMatrix));

        // update price group
        $this->priceGroupRepository->add($priceGroup);
        $priceGroup->publishEvents();
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield ImportMaterialDataType::class => [
            'method' => 'handleImportMaterialDataType',
            'bus' => 'command_bus',
            'aptoMessagePrefix' => 'ImportExport'
        ];

        yield ImportMaterialPriceGroupDataType::class => [
            'method' => 'handleImportMaterialPriceGroupDataType',
            'bus' => 'command_bus',
            'aptoMessagePrefix' => 'ImportExport'
        ];
    }

    /**
     * @param string $name
     * @param string $locale
     * @return AptoUuid|null
     * @throws InvalidUuidException
     */
    private function getPriceMatrixId(string $name, string $locale): ?AptoUuid
    {
        // name not valid return null
        $name = trim(NameSanitizer::sanitizeName($name));
        if ('' === $name) {
            return null;
        }

        // return existing price matrix id
        $priceMatrixId = $this->priceMatrixRepository->findFirstIdByName($name);
        if (null !== $priceMatrixId) {
            return new AptoUuid($priceMatrixId);
        }

        // create new price matrix
        $priceMatrix = new PriceMatrixEntity(
            $this->priceMatrixRepository->nextIdentity(),
            $this->getTranslatedValue([$locale => $name])
        );

        $this->priceMatrixRepository->add($priceMatrix);
        $priceMatrix->publishEvents();

        return $priceMatrix->getId();
    }

    /**
     * @param string $locale
     * @param string $poolName
     * @return Pool
     * @throws InvalidUuidException
     */
    private function getPool(string $locale, string $poolName): Pool
    {
        // get pool
        $poolId = $this->poolRepository->findFirstIdByName($poolName);
        if (null !== $poolId) {
            return $this->poolRepository->findById($poolId);
        }

        // add pool
        $pool = new Pool(
            $this->poolRepository->nextIdentity(),
            $this->getTranslatedValue([$locale => $poolName])
        );

        $this->poolRepository->add($pool);
        $pool->publishEvents();
        return $pool;
    }

    /**
     * @param string $locale
     * @param string $name
     * @param string $internalName
     * @return PriceGroup
     * @throws InvalidUuidException
     */
    private function getPriceGroup(string $locale, string $name, string $internalName): PriceGroup
    {
        // get price group
        $priceGroupId = $this->priceGroupRepository->findFirstIdByInternalName($internalName);
        if (null !== $priceGroupId) {
            return $this->priceGroupRepository->findById($priceGroupId);
        }

        // add price group
        $priceGroup = new PriceGroup(
            $this->priceGroupRepository->nextIdentity(),
            $this->getTranslatedValue([$locale => $name]),
            $this->getTranslatedValue([$locale => $internalName]),
            0.0
        );

        $this->priceGroupRepository->add($priceGroup);
        $priceGroup->publishEvents();
        return $priceGroup;
    }

    /**
     * @param string $value
     * @return int
     */
    private function convertToInt(string $value): int
    {
        return (int) $value;
    }
}
