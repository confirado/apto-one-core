<?php

namespace Apto\Base\Application\Backend\Commands\Settings;

use Apto\Base\Application\Core\Commands\AbstractCommandHandler;
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
     * @param SettingsRepository $settingsRepository
     */
    public function __construct(SettingsRepository $settingsRepository)
    {
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * @param AddSettings $command
     * @return void
     * @throws InvalidUuidException
     */
    public function handleAddSettings(AddSettings $command): void
    {
        $settings = new Settings(
            $this->settingsRepository->nextIdentity(),
            $command->getPrimaryColor(),
            $command->getSecondaryColor(),
            $command->getBackgroundColorHeader(),
            $command->getFontColorHeader(),
            $command->getBackgroundColorFooter(),
            $command->getFontColorFooter()
        );

        $this->settingsRepository->add($settings);
        $settings->publishEvents();
    }

    /**
     * @param UpdateSettings $command
     * @return void
     */
    public function handleUpdateSettings(UpdateSettings $command): void
    {
        $settings = $this->settingsRepository->findById($command->getId());
        if (null === $settings) {
            return;
        }

        $settings->setPrimaryColor($command->getPrimaryColor());
        $settings->setSecondaryColor($command->getSecondaryColor());
        $settings->setBackgroundColorHeader($command->getBackgroundColorHeader());
        $settings->setFontColorHeader($command->getFontColorHeader());
        $settings->setBackgroundColorFooter($command->getBackgroundColorFooter());
        $settings->setFontColorFooter($command->getFontColorFooter());

        $this->settingsRepository->update($settings);
        $settings->publishEvents();
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
