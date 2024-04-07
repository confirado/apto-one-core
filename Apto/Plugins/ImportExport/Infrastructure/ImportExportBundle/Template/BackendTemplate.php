<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Template;

use Apto\Base\Infrastructure\AptoBaseBundle\Template\BackendTemplateInterface;

class BackendTemplate implements BackendTemplateInterface
{
    /**
     * @return array
     */
    public function getTemplates():array
    {
        $data = [
            'angularTemplates' => [
                '@ImportExport/pages/import-export/import.html.twig'
            ]
        ];
        return [$data, 'backend'];
    }

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        $data = [
            'angular' => [
                '/import-export/import' => [
                    'templateUrl' => '@ImportExport/pages/import-export/import.html.twig',
                    'controller' => 'PluginImportExportImportController'
                ],
            ]
        ];
        return [
            $data, 'backend'
        ];
    }

    /**
     * @return array
     */
    public function getMainMenuEntries(): array
    {
        $data = [
            '/import-export/' => [
                'route' => '/import-export/import',
                'label' => 'Import',
                'icon' => 'f03a',
                'aclMessagesRequired' => json_encode([
                    'commands' => ['ImportExportImportDefaultDataType'],
                    'queries' => [],
                    'strategy' => 'one'
                ]),
            ]
        ];
        return [
            $data, 'backend'
        ];
    }
}

