<?php

namespace Apto\Plugins\PartsList\Infrastructure\AptoPartsListBundle\Template;

use Apto\Base\Infrastructure\AptoBaseBundle\Template\BackendTemplateInterface;

class BackendTemplate implements BackendTemplateInterface
{
    const ANGULAR_TEMPLATES = [
        'parts-list' => '@AptoPartsList/apto/parts-list/pages/parts-list/parts-list.html.twig',
        'units-list' => '@AptoPartsList/apto/parts-list/pages/units-list/units-list.html.twig'
    ];

    /**
     * @return array
     */
    public function getTemplates(): array
    {
        $data = [
            'angularTemplates' => [
                self::ANGULAR_TEMPLATES['parts-list'],
                self::ANGULAR_TEMPLATES['units-list']
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
                '/parts-list/parts/' => [
                    'templateUrl' => self::ANGULAR_TEMPLATES['parts-list'],
                    'controller' => 'AptoPartsListPartController'
                ],
                '/parts-list/units/' => [
                    'templateUrl' => self::ANGULAR_TEMPLATES['units-list'],
                    'controller' => 'AptoPartsListUnitController'
                ]
            ]
        ];
        return [$data, 'backend'];
    }

    /**
     * @return array
     */
    public function getMainMenuEntries(): array
    {
        $data = [
            '/parts-list/' => [
                'route' => '/parts-list/',
                'label' => 'Stückliste',
                'icon' => 'f03a',
                'aclMessagesRequired' => json_encode([
                    'commands' => [],
                    'queries' => ['AptoPartsListFindParts', 'AptoPartsListFindUnits'],
                    'strategy' => 'one'
                ]),
                'subItems' => [
                    [
                        'route' => '/parts-list/parts/',
                        'label' => 'Teile',
                        'icon' => 'f03a',
                        'aclMessagesRequired' => json_encode([
                            'commands' => [],
                            'queries' => ['AptoPartsListFindParts'],
                            'strategy' => 'all'
                        ])
                    ],
                    [
                        'route' => '/parts-list/units/',
                        'label' => 'Einheiten',
                        'icon' => 'f03a',
                        'aclMessagesRequired' => json_encode([
                            'commands' => [],
                            'queries' => ['AptoPartsListFindUnits'],
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

