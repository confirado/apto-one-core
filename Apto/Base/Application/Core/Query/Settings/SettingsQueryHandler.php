<?php

namespace Apto\Base\Application\Core\Query\Settings;

use Apto\Base\Application\Core\QueryHandlerInterface;

class SettingsQueryHandler implements QueryHandlerInterface
{
    /**
     * @var SettingsFinder
     */
    private SettingsFinder $settingsFinder;

    /**
     * @param SettingsFinder $settingsFinder
     */
    public function __construct(SettingsFinder $settingsFinder)
    {
        $this->settingsFinder = $settingsFinder;
    }

    /**
     * @param FindSettings $query
     * @return array|null
     */
    public function handleFindSettings(FindSettings $query): ?array
    {
        return $this->settingsFinder->findSettings();
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindSettings::class => [
            'method' => 'handleFindSettings',
            'bus' => 'query_bus'
        ];
    }
}
