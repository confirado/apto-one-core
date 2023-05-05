<?php

namespace Apto\Plugins\MaterialPickerElement\Infrastructure\MaterialPickerElementBundle\Template;

use Apto\Base\Infrastructure\AptoBaseBundle\Template\BackendTemplateInterface;

class BackendTemplate implements BackendTemplateInterface
{
    /**
     * @return array
     */
    public function getTemplates(): array
    {
        $data = [
            'angularTemplates' => [
                '@MaterialPickerElement/material-picker-element/pages/material.html.twig',
                '@MaterialPickerElement/material-picker-element/pages/price-group.html.twig',
                '@MaterialPickerElement/material-picker-element/pages/pool.html.twig',
                '@MaterialPickerElement/material-picker-element/pages/property.html.twig'
            ]
        ];
        return [
            $data, 'backend'
        ];
    }

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        $data = [
            'angular' => [
                '/plugin/material-picker/material/' => [
                    'templateUrl' => '@MaterialPickerElement/material-picker-element/pages/material.html.twig',
                    'controller' => 'MaterialPickerMaterialController'
                ], '/plugin/material-picker/price-group/' => [
                    'templateUrl' => '@MaterialPickerElement/material-picker-element/pages/price-group.html.twig',
                    'controller' => 'MaterialPickerPriceGroupController'
                ], '/plugin/material-picker/pool/' => [
                    'templateUrl' => '@MaterialPickerElement/material-picker-element/pages/pool.html.twig',
                    'controller' => 'MaterialPickerPoolController'
                ], '/plugin/material-picker/property/' => [
                    'templateUrl' => '@MaterialPickerElement/material-picker-element/pages/property.html.twig',
                    'controller' => 'MaterialPickerGroupController'
                ]
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
            '/plugin/material-picker/' => [
                'label' => 'Stoffverwaltung',
                'icon' => 'f279',
                'aclMessagesRequired' => json_encode([
                    'commands' => [],
                    'queries' => ['FindMaterialPickerMaterialsByPage', 'FindMaterialPickerPriceGroupsByPage', 'FindMaterialPickerGroupsByPage', 'FindMaterialPickerPoolsByPage'],
                    'strategy' => 'one'
                ]),
                'subItems' => [
                    [
                        'route' => '/plugin/material-picker/material/',
                        'label' => 'Stoffe',
                        'icon' => 'f1c0',
                        'aclMessagesRequired' => json_encode([
                            'commands' => [],
                            'queries' => ['FindMaterialPickerMaterialsByPage'],
                            'strategy' => 'all'
                        ])
                    ], [
                        'route' => '/plugin/material-picker/price-group/',
                        'label' => 'Preisgruppen',
                        'icon' => 'f1c0',
                        'aclMessagesRequired' => json_encode([
                            'commands' => [],
                            'queries' => ['FindMaterialPickerPriceGroupsByPage'],
                            'strategy' => 'all'
                        ])
                    ], [
                        'route' => '/plugin/material-picker/property/',
                        'label' => 'Eigenschaften',
                        'icon' => 'f1c0',
                        'aclMessagesRequired' => json_encode([
                            'commands' => [],
                            'queries' => ['FindMaterialPickerGroupsByPage'],
                            'strategy' => 'all'
                        ])
                    ], [
                        'route' => '/plugin/material-picker/pool/',
                        'label' => 'Pools',
                        'icon' => 'f277',
                        'aclMessagesRequired' => json_encode([
                            'commands' => [],
                            'queries' => ['FindMaterialPickerPoolsByPage'],
                            'strategy' => 'all'
                        ])
                    ]
                ]
            ]
        ];
        return [
            $data, 'backend'
        ];
    }
}

