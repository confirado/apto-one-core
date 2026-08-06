<?php

namespace Apto\Base\Application\Backend\Commands\Settings;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
use Apto\Base\Domain\Core\Model\FileSystem\AssetsFileSystemConnector;
use Apto\Base\Domain\Core\Model\FileSystem\Directory\Directory;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileNotCreatableException;
use Apto\Base\Domain\Core\Model\FileSystem\Exception\FileSystemMountedReadOnlyException;
use Apto\Base\Domain\Core\Model\FileSystem\File\File;
use Apto\Base\Domain\Core\Model\FileSystem\RootFileSystemConnector;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Base\Domain\Core\Model\Settings\Settings;
use Apto\Base\Domain\Core\Model\Settings\SettingsRepository;

class SettingsCommandHandler extends AbstractCommandHandler
{
    /**
     * @var SettingsRepository
     */
    private SettingsRepository $settingsRepository;

    /**
     * @var AssetsFileSystemConnector
     */
    private AssetsFileSystemConnector $assetsFileSystemConnector;

    /**
     * @param SettingsRepository $settingsRepository
     * @param AssetsFileSystemConnector $assetsFileSystemConnector
     */
    public function __construct(SettingsRepository $settingsRepository, AssetsFileSystemConnector $assetsFileSystemConnector)
    {
        $this->settingsRepository = $settingsRepository;
        $this->assetsFileSystemConnector = $assetsFileSystemConnector;
    }

    /**
     * @param AddSettings $command
     * @return void
     * @throws FileNotCreatableException
     * @throws FileSystemMountedReadOnlyException
     * @throws InvalidUuidException
     */
    public function handleAddSettings(AddSettings $command): void
    {
        $settings = new Settings(
            $this->settingsRepository->nextIdentity(),
            $command->getColorPrimary(),
            $command->getColorPrimaryHover(),
            $command->getColorAccent(),
            $command->getColorBackgroundHeader(),
            $command->getColorBackgroundFooter(),
            $command->getColorTitle(),
            $command->getColorText()
        );
        $this->updateSettingsCssFile($settings);
        $this->settingsRepository->add($settings);
        $settings->publishEvents();
    }

    /**
     * @param UpdateSettings $command
     * @return void
     * @throws FileNotCreatableException
     * @throws FileSystemMountedReadOnlyException
     */
    public function handleUpdateSettings(UpdateSettings $command): void
    {
        $settings = $this->settingsRepository->findById($command->getId());
        if (null === $settings) {
            return;
        }

        $settings->setColorPrimary($command->getColorPrimary());
        $settings->setColorPrimaryHover($command->getColorPrimaryHover());
        $settings->setColorAccent($command->getColorAccent());
        $settings->setColorBackgroundHeader($command->getColorBackgroundHeader());
        $settings->setColorBackgroundFooter($command->getColorBackgroundFooter());
        $settings->setColorTitle($command->getColorTitle());
        $settings->setColorText($command->getColorText());

        $this->updateSettingsCssFile($settings);
        $this->settingsRepository->update($settings);
        $settings->publishEvents();
    }

    /**
     * @return void
     * @throws FileNotCreatableException
     * @throws FileSystemMountedReadOnlyException
     */
    private function updateSettingsCssFile(Settings $settings): void
    {
        // create file object
        $settingsCssDirectory = new Directory('/css');
        $settingsCss = new File($settingsCssDirectory, 'settings.css');

        // create empty file
        $this->assetsFileSystemConnector->createFile($settingsCss, null, null, true);

        // put file content to settings.css
        file_put_contents($this->assetsFileSystemConnector->getAbsolutePath($settingsCss->getPath()), $this->getSettingsCssString($settings));
    }

    private function getSettingsCssString(Settings $settings): string
    {
        return '#shop-template-apto { '
            . '--color-primary: ' . $settings->getColorPrimary() . ';'
            . '--color-primary-hover: ' . $settings->getColorPrimaryHover() . ';'
            . '--color-accent: ' . $settings->getColorAccent() . ';'
            . '--color-background-header: ' . $settings->getColorBackgroundHeader() . ';'
            . '--color-background-footer: ' . $settings->getColorBackgroundFooter() . ';'
            . '--color-title: ' . $settings->getColorTitle() . ';'
            . '--color-text: ' . $settings->getColorText() . ';'
            . '}';
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield AddSettings::class => [
            'method' => 'handleAddSettings',
            'bus' => 'command_bus'
        ];

        yield UpdateSettings::class => [
            'method' => 'handleUpdateSettings',
            'bus' => 'command_bus'
        ];
    }
}
