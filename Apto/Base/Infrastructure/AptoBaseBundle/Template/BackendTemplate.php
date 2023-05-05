<?php
namespace Apto\Base\Infrastructure\AptoBaseBundle\Template;

class BackendTemplate implements BackendTemplateInterface
{
    /**
     * @return array
     */
    public function getTemplates(): array
    {
        $data = [
            'angularTemplates' => [
                '@AptoBase/apto/base/backend/pages/home/home.html.twig',
                '@AptoBase/apto/base/backend/pages/domain-event-log/domain-event-log.html.twig',
                '@AptoBase/apto/base/backend/pages/language/language.html.twig',
                '@AptoBase/apto/base/backend/pages/message-bus-firewall/message-bus-firewall.html.twig',
                '@AptoBase/apto/base/backend/pages/user/user.html.twig',
                '@AptoBase/apto/base/backend/pages/user-role/user-role.html.twig',
                '@AptoBase/apto/base/backend/pages/media/media.html.twig',
                '@AptoBase/apto/base/backend/pages/customer/customer.html.twig',
                '@AptoBase/apto/base/backend/pages/customer-group/customer-group.html.twig',
                '@AptoBase/apto/base/backend/pages/content-snippet/content-snippet.html.twig'
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
                '/' => [
                    'templateUrl' => '@AptoBase/apto/base/backend/pages/home/home.html.twig',
                    'controller' => 'AptoHomeController'
                ],
                '/language/' => [
                    'templateUrl' => '@AptoBase/apto/base/backend/pages/language/language.html.twig',
                    'controller' => 'LanguageController'
                ],
                '/message-bus-firewall/' => [
                    'templateUrl' => '@AptoBase/apto/base/backend/pages/message-bus-firewall/message-bus-firewall.html.twig',
                    'controller' => 'MessageBusFirewallController'
                ],
                '/domain-event-log/' => [
                    'templateUrl' => '@AptoBase/apto/base/backend/pages/domain-event-log/domain-event-log.html.twig',
                    'controller' => 'DomainEventController'
                ],
                '/user/' => [
                    'templateUrl' => '@AptoBase/apto/base/backend/pages/user/user.html.twig',
                    'controller' => 'UserController'
                ],
                '/user-role/' => [
                    'templateUrl' => '@AptoBase/apto/base/backend/pages/user-role/user-role.html.twig',
                    'controller' => 'UserRoleController'
                ],
                '/media/' => [
                    'templateUrl' => '@AptoBase/apto/base/backend/pages/media/media.html.twig',
                    'controller' => 'MediaController'
                ],
                '/customer/' => [
                    'templateUrl' => '@AptoBase/apto/base/backend/pages/customer/customer.html.twig',
                    'controller' => 'CustomerController'
                ],
                '/customer-group/' => [
                    'templateUrl' => '@AptoBase/apto/base/backend/pages/customer-group/customer-group.html.twig',
                    'controller' => 'CustomerGroupController'
                ],
                '/content-snippet/' => [
                    'templateUrl' => '@AptoBase/apto/base/backend/pages/content-snippet/content-snippet.html.twig',
                    'controller' => 'ContentSnippetController'
                ]
            ],
            'routeNames' => [
                'base_url' => 'apto_base_infrastructure_aptobase_backend_index',
                'thumb_url' => 'apto_base_infrastructure_aptobase_thumbnail_getthumbnail',
                'messagebus_command' => 'apto_base_infrastructure_aptobase_messagebus_command_backend',
                'messagebus_query' => 'apto_base_infrastructure_aptobase_messagebus_query_backend',
                'messagebus_batchexecute' => 'apto_base_infrastructure_aptobase_messagebus_batchexecute_backend',
                'messagebus_messagesisgranted' => 'apto_base_infrastructure_aptobase_messagebus_messagesisgranted_backend'
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
            '/' => [
                'route' => '/',
                'label' => 'Home',
                'icon' => 'f015'
            ],

            '/language/' => [
                'route' => '/language/',
                'label' => 'Sprachen',
                'icon' => 'f0ac',
                'aclMessagesRequired' => json_encode([
                    'commands' => [],
                    'queries' => ['FindLanguages'],
                    'strategy' => 'all'
                ])
            ],

            '/domain-event-log/' => [
                'route' => '/domain-event-log/',
                'label' => 'Event Log',
                'icon' => 'f1c0',
                'aclMessagesRequired' => json_encode([
                    'commands' => [],
                    'queries' => ['FindDomainEventLog', 'FindGroupedTypeNames', 'FindGroupedUserIds', 'FindUsersByUserIds'],
                    'strategy' => 'all'
                ])
            ],

            '/media/' => [
                'route' => '/media/',
                'label' => 'Medien',
                'icon' => 'f1c5',
                'aclMessagesRequired' => json_encode([
                    'commands' => ['AddMediaFile'], // 'RemoveMediaFile', 'UploadMediaFile'
                    'queries' => ['ListMediaFiles', 'FindMediaFile', 'FindMediaFileByName'],
                    'strategy' => 'all'
                ])
            ],

            '/security/' => [
                'route' => '/security/',
                'label' => 'Security',
                'icon' => 'f132',
                'aclMessagesRequired' => json_encode([
                    'commands' => [],
                    'queries' => ['FindMessageBusMessages', 'FindUsers', 'FindUserRoles'],
                    'strategy' => 'one'
                ]),
                'subItems' => [
                    [
                        'route' => '/message-bus-firewall/',
                        'label' => 'Firewall',
                        'icon' => 'f2c5',
                        'aclMessagesRequired' => json_encode([
                            'commands' => [],
                            'queries' => ['FindMessageBusMessages'],
                            'strategy' => 'all'
                        ])
                    ], [
                        'route' => '/user/',
                        'label' => 'Benutzer',
                        'icon' => 'f007',
                        'aclMessagesRequired' => json_encode([
                            'commands' => [],
                            'queries' => ['FindUsers'],
                            'strategy' => 'all'
                        ])
                    ], [
                        'route' => '/user-role/',
                        'label' => 'Benutzerrollen',
                        'icon' => 'f0c0',
                        'aclMessagesRequired' => json_encode([
                            'commands' => [],
                            'queries' => ['FindUserRoles'],
                            'strategy' => 'all'
                        ])
                    ]
                ]
            ],

            '/shop-connector/' => [
                'route' => '/shop/',
                'label' => 'Shop Connector',
                'icon' => 'f07a',
                'aclMessagesRequired' => json_encode([
                    'commands' => [],
                    'queries' => ['FindCustomers', 'FindCustomerGroups'],
                    'strategy' => 'one'
                ]),
                'subItems' => [
                    [
                        'route' => '/customer/',
                        'label' => 'Kunden',
                        'icon' => 'f007',
                        'aclMessagesRequired' => json_encode([
                            'commands' => [],
                            'queries' => ['FindCustomers'],
                            'strategy' => 'all'
                        ])
                    ], [
                        'route' => '/customer-group/',
                        'label' => 'Kundengruppen',
                        'icon' => 'f0c0',
                        'aclMessagesRequired' => json_encode([
                            'commands' => [],
                            'queries' => ['FindCustomerGroups'],
                            'strategy' => 'all'
                        ])
                    ]
                ]
            ],

            '/content-snippet/' => [
                'route' => '/content-snippet/',
                'label' => 'Content Snippet',
                'icon' => 'f03a',
                'aclMessagesRequired' => json_encode([
                    'commands' => [],
                    'queries' => ['FindContentSnippetTree'],
                    'strategy' => 'all'
                ]),
            ]

        ];
        return [$data, 'backend'];
    }
}

