<?php

namespace Apto\Plugins\MaterialPickerElement\Application\Backend\Commands\Material;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Base\Domain\Core\Model\AptoPrice\AptoPriceDuplicateException;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\Color;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventPublisher;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFile;
use Apto\Base\Domain\Core\Model\MediaFile\MediaFileRepository;
use Apto\Catalog\Domain\Core\Model\Product\Condition\CriterionInvalidOperatorException;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material\Material;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material\MaterialRemoved;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Material\MaterialRepository;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Pool\PoolRepository;
use Apto\Plugins\MaterialPickerElement\Domain\Core\Model\Property\PropertyRepository;
use Money\Currency;
use Money\Money;

class MaterialCommandHandler extends AbstractCommandHandler
{
    /**
     * @var MaterialRepository
     */
    protected $materialRepository;

    /**
     * @var PropertyRepository
     */
    protected $propertyRepository;

    /**
     * @var PoolRepository
     */
    protected $poolRepository;

    /**
     * @var MediaFileRepository
     */
    protected $mediaFileRepository;

    /**
     * @var MediaFileSystemConnector
     */
    protected $fileSystemConnector;

    /**
     * @param MaterialRepository $materialRepository
     * @param PropertyRepository $propertyRepository
     * @param PoolRepository $poolRepository
     * @param MediaFileRepository $mediaFileRepository
     * @param MediaFileSystemConnector $fileSystemConnector
     */
    public function __construct(
        MaterialRepository $materialRepository,
        PropertyRepository $propertyRepository,
        PoolRepository $poolRepository,
        MediaFileRepository $mediaFileRepository,
        MediaFileSystemConnector $fileSystemConnector
    ) {
        $this->materialRepository = $materialRepository;
        $this->propertyRepository = $propertyRepository;
        $this->poolRepository = $poolRepository;
        $this->mediaFileRepository = $mediaFileRepository;
        $this->fileSystemConnector = $fileSystemConnector;
    }

    /**
     * @param AddMaterial $command
     * @return void
     * @throws CriterionInvalidOperatorException
     * @throws InvalidUuidException
     */
    public function handleAddMaterial(AddMaterial $command)
    {
        $material = new Material(
            $this->materialRepository->nextIdentity(),
            $this->getTranslatedValue($command->getName())
        );

        $material
            ->setActive($command->getActive())
            ->setIsNotAvailable($command->getIsNotAvailable())
            ->setIdentifier($command->getIdentifier())
            ->setDescription($this->getTranslatedValue($command->getDescription()))
            ->setClicks($command->getClicks())
            ->setReflection($command->getReflection())
            ->setTransmission($command->getTransmission())
            ->setAbsorption($command->getAbsorption())
            ->setPosition($command->getPosition())
            ->setConditionsOperator($command->getConditionsOperator());

        if ($command->getPreviewImage()) {
            $mediaFile = $this->getMediaFile($command->getPreviewImage());
            $material->setPreviewImage(
                $mediaFile
            );
        } else {
            $material->removePreviewImage();
        }

        $this->materialRepository->add($material);
        $material->publishEvents();
    }

    /**
     * @param UpdateMaterial $command
     * @return void
     * @throws InvalidUuidException
     * @throws CriterionInvalidOperatorException
     */
    public function handleUpdateMaterial(UpdateMaterial $command)
    {
        $material = $this->materialRepository->findById($command->getId());

        if (null !== $material) {

            $material
                ->setActive($command->getActive())
                ->setIsNotAvailable($command->getIsNotAvailable())
                ->setIdentifier($command->getIdentifier())
                ->setName($this->getTranslatedValue($command->getName()))
                ->setDescription($this->getTranslatedValue($command->getDescription()))
                ->setClicks($command->getClicks())
                ->setReflection($command->getReflection())
                ->setTransmission($command->getTransmission())
                ->setAbsorption($command->getAbsorption())
                ->setPosition($command->getPosition())
                ->setConditionsOperator($command->getConditionsOperator());

            if ($command->getPreviewImage()) {
                $mediaFile = $this->getMediaFile($command->getPreviewImage());
                $material->setPreviewImage(
                    $mediaFile
                );
            } else {
                $material->removePreviewImage();
            }

            $this->materialRepository->update($material);
            $material->publishEvents();
        }
    }

    /**
     * @param RemoveMaterial $command
     * @return void
     */
    public function handleRemoveMaterial(RemoveMaterial $command)
    {
        $material = $this->materialRepository->findById($command->getId());

        if (null !== $material) {
            $this->materialRepository->remove($material);
            DomainEventPublisher::instance()->publish(
                new MaterialRemoved(
                    $material->getId()
                )
            );
        }
    }

    /**
     * @param AddMaterialPrice $command
     * @return void
     * @throws AptoPriceDuplicateException
     * @throws InvalidUuidException
     */
    public function handleAddMaterialPrice(AddMaterialPrice $command)
    {
        $material = $this->materialRepository->findById($command->getId());

        if (null !== $material) {
            $material->addAptoPrice(
                new Money(
                    $command->getAmount(),
                    new Currency(
                        $command->getCurrency()
                    )
                ),
                new AptoUuid(
                    $command->getCustomerGroupId()
                )
            );

            $this->materialRepository->update($material);
            $material->publishEvents();
        }
    }

    /**
     * @param RemoveMaterialPrice $command
     * @return void
     * @throws InvalidUuidException
     */
    public function handleRemoveMaterialPrice(RemoveMaterialPrice $command)
    {
        $material = $this->materialRepository->findById($command->getId());

        if (null === $material) {
            return;
        }

        $material->removeAptoPrice(
            new AptoUuid(
                $command->getPriceId()
            )
        );

        $this->materialRepository->update($material);
        $material->publishEvents();
    }

    /**
     * @param AddMaterialCondition $command
     *
     * @return void
     * @throws AptoPriceDuplicateException
     * @throws InvalidUuidException
     */
    public function handleAddMaterialPickerMaterialConditionSet(AddMaterialCondition $command): void
    {
        $material = $this->materialRepository->findById($command->getId());

        if (null === $material) {
            return;
        }

        $material->addConditionSet(new AptoUuid($command->getConditionId()));
        $this->materialRepository->update($material);
        $material->publishEvents();
    }

    /**
     * @param RemoveMaterialCondition $command
     *
     * @return void
     * @throws InvalidUuidException
     */
    public function handleRemoveMaterialPickerMaterialConditionSet(RemoveMaterialCondition $command): void
    {
        $material = $this->materialRepository->findById($command->getId());

        if (null === $material) {
            return;
        }

        $material->removeConditionSet(new AptoUuid($command->getConditionId()));
        $this->materialRepository->update($material);
        $material->publishEvents();
    }

    /**
     * @param AddMaterialProperty $command
     * @return void
     */
    public function handleAddMaterialProperty(AddMaterialProperty $command)
    {
        $material = $this->materialRepository->findById($command->getMaterialId());
        $property = $this->propertyRepository->findById($command->getPropertyId());

        if (null !== $material && null !== $property) {
            $material->addProperty($property);
            $this->materialRepository->update($material);
            $material->publishEvents();
        }
    }

    /**
     * @param RemoveMaterialProperty $command
     * @throws InvalidUuidException
     */
    public function handleRemoveMaterialProperty(RemoveMaterialProperty $command)
    {
        $material = $this->materialRepository->findById($command->getMaterialId());

        if (null !== $material) {
            $material->removeProperty(
                new AptoUuid($command->getPropertyId())
            );
            $this->materialRepository->update($material);
            $material->publishEvents();
        }
    }

    /**
     * @param AddMaterialColorRating $command
     * @return void
     */
    public function handleAddMaterialColorRating(AddMaterialColorRating $command)
    {
        $material = $this->materialRepository->findById($command->getMaterialId());

        if (null !== $material) {
            $material->addColorRating(
                Color::fromHex($command->getColor()),
                $command->getRating()
            );

            $this->materialRepository->update($material);
            $material->publishEvents();
        }
    }

    /**
     * @param RemoveMaterialColorRating $command
     * @return void
     * @throws InvalidUuidException
     */
    public function handleRemoveMaterialColorRating(RemoveMaterialColorRating $command)
    {
        $material = $this->materialRepository->findById($command->getMaterialId());

        if (null !== $material) {
            $material->removeColorRating(
                new AptoUuid($command->getColorRatingId())
            );

            $this->materialRepository->update($material);
            $material->publishEvents();
        }
    }

    /**
     * @param AddMaterialGalleryImage $command
     * @return void
     * @throws InvalidUuidException
     */
    public function handleAddMaterialGalleryImage(AddMaterialGalleryImage $command)
    {
        $material = $this->materialRepository->findById($command->getMaterialId());

        if (null !== $material) {

            if ($command->getGalleryImage()) {
                $mediaFile = $this->getMediaFile($command->getGalleryImage());
                $material->addGalleryImage(
                    $mediaFile
                );
                $this->materialRepository->update($material);
                $material->publishEvents();
            }
        }
    }

    /**
     * @param RemoveMaterialGalleryImage $command
     * @return void
     * @throws InvalidUuidException
     */
    public function handleRemoveMaterialGalleryImage(RemoveMaterialGalleryImage $command)
    {
        $material = $this->materialRepository->findById($command->getMaterialId());

        if (null !== $material) {
            $material->removeGalleryImage(
                new AptoUuid($command->getGalleryImageId())
            );
            $this->materialRepository->update($material);
            $material->publishEvents();
        }
    }

    /**
     * @param AddMaterialRenderImage $command
     * @return void
     * @throws InvalidUuidException
     */
    public function handleAddMaterialRenderImage(AddMaterialRenderImage $command)
    {
        $material = $this->materialRepository->findById($command->getMaterialId());
        $mediaFile = $this->getMediaFile($command->getFile());
        $pool = $this->poolRepository->findById($command->getPoolId());

        $material->addRenderImage(
            $command->getLayer(),
            $command->getPerspective(),
            $mediaFile,
            $pool,
            $command->getOffsetX(),
            $command->getOffsetY()
        );

        $this->materialRepository->update($material);
        $material->publishEvents();
    }

    /**
     * @param RemoveMaterialRenderImage $command
     * @return void
     * @throws InvalidUuidException
     */
    public function handleRemoveMaterialRenderImage(RemoveMaterialRenderImage $command)
    {
        $material = $this->materialRepository->findById($command->getMaterialId());

        if (null === $material) {
            return;
        }

        $material->removeRenderImage(
            new AptoUuid(
                $command->getRenderImageId()
            )
        );

        $this->materialRepository->update($material);
        $material->publishEvents();
    }

    /**
     * @param string $path
     * @return MediaFile
     * @throws InvalidUuidException
     */
    protected function getMediaFile(string $path): MediaFile
    {
        $file = File::createFromPath($path);

        $mediaFile = $this->mediaFileRepository->findOneByFile($file);
        if (null === $mediaFile) {
            $mediaFile = new MediaFile(
                $this->mediaFileRepository->nextIdentity(),
                $file
            );
            $mediaFile
                ->setSize($this->fileSystemConnector->getFileSize($file))
                ->setMd5Hash($this->fileSystemConnector->getFileMd5Hash($file));

            $this->mediaFileRepository->add($mediaFile);
            $mediaFile->publishEvents();
        }

        return $mediaFile;
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddMaterial::class => [
            'method' => 'handleAddMaterial',
            'bus' => 'command_bus',
            'aptoMessageName' => 'AddMaterialPickerMaterial'
        ];

        yield UpdateMaterial::class => [
            'method' => 'handleUpdateMaterial',
            'bus' => 'command_bus',
            'aptoMessageName' => 'UpdateMaterialPickerMaterial'
        ];

        yield RemoveMaterial::class => [
            'method' => 'handleRemoveMaterial',
            'bus' => 'command_bus',
            'aptoMessageName' => 'RemoveMaterialPickerMaterial'
        ];

        yield AddMaterialPrice::class => [
            'method' => 'handleAddMaterialPrice',
            'bus' => 'command_bus',
            'aptoMessageName' => 'AddMaterialPickerMaterialPrice'
        ];

        yield RemoveMaterialPrice::class => [
            'method' => 'handleRemoveMaterialPrice',
            'bus' => 'command_bus',
            'aptoMessageName' => 'RemoveMaterialPickerMaterialPrice'
        ];

        yield AddMaterialCondition::class => [
            'method' => 'handleAddMaterialPickerMaterialConditionSet',
            'bus' => 'command_bus',
            'aptoMessageName' => 'AddMaterialPickerMaterialConditionSet'
        ];

        yield RemoveMaterialCondition::class => [
            'method' => 'handleRemoveMaterialPickerMaterialConditionSet',
            'bus' => 'command_bus',
            'aptoMessageName' => 'RemoveMaterialPickerMaterialConditionSet'
        ];

        yield AddMaterialProperty::class => [
            'method' => 'handleAddMaterialProperty',
            'bus' => 'command_bus',
            'aptoMessageName' => 'AddMaterialPickerMaterialProperty'
        ];

        yield RemoveMaterialProperty::class => [
            'method' => 'handleRemoveMaterialProperty',
            'bus' => 'command_bus',
            'aptoMessageName' => 'RemoveMaterialPickerMaterialProperty'
        ];

        yield AddMaterialColorRating::class => [
            'method' => 'handleAddMaterialColorRating',
            'bus' => 'command_bus',
            'aptoMessageName' => 'AddMaterialPickerMaterialColorRating'
        ];

        yield RemoveMaterialColorRating::class => [
            'method' => 'handleRemoveMaterialColorRating',
            'bus' => 'command_bus',
            'aptoMessageName' => 'RemoveMaterialPickerMaterialColorRating'
        ];

        yield AddMaterialGalleryImage::class => [
            'method' => 'handleAddMaterialGalleryImage',
            'bus' => 'command_bus',
            'aptoMessageName' => 'AddMaterialPickerMaterialGalleryImage'
        ];

        yield RemoveMaterialGalleryImage::class => [
            'method' => 'handleRemoveMaterialGalleryImage',
            'bus' => 'command_bus',
            'aptoMessageName' => 'RemoveMaterialPickerMaterialGalleryImage'
        ];

        yield AddMaterialRenderImage::class => [
            'method' => 'handleAddMaterialRenderImage',
            'bus' => 'command_bus',
            'aptoMessageName' => 'AddMaterialPickerMaterialRenderImage'
        ];

        yield RemoveMaterialRenderImage::class => [
            'method' => 'handleRemoveMaterialRenderImage',
            'bus' => 'command_bus',
            'aptoMessageName' => 'RemoveMaterialPickerMaterialRenderImage'
        ];
    }
}
