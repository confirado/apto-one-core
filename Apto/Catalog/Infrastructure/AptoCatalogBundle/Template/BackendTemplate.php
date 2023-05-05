<?php
namespace Apto\Catalog\Infrastructure\AptoCatalogBundle\Template;

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
                '@AptoCatalog/apto/catalog/backend/pages/shop/shop.html.twig',
                '@AptoCatalog/apto/catalog/backend/pages/category/category.html.twig',
                '@AptoCatalog/apto/catalog/backend/pages/product/product.html.twig',
                '@AptoCatalog/apto/catalog/backend/pages/group/group.html.twig',
                '@AptoCatalog/apto/catalog/backend/pages/price-matrix/price-matrix.html.twig',
                '@AptoCatalog/apto/catalog/backend/pages/filter/filter-property.html.twig',
                '@AptoCatalog/apto/catalog/backend/pages/filter/filter-category.html.twig',
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
                '/shop/' => [
                    'templateUrl' => '@AptoCatalog/apto/catalog/backend/pages/shop/shop.html.twig',
                    'controller' => 'ShopController'
                ],
                '/category/' => [
                    'templateUrl' => '@AptoCatalog/apto/catalog/backend/pages/category/category.html.twig',
                    'controller' => 'CategoryController'
                ],
                '/product/' => [
                    'templateUrl' => '@AptoCatalog/apto/catalog/backend/pages/product/product.html.twig',
                    'controller' => 'ProductController'
                ],
                '/group/' => [
                    'templateUrl' => '@AptoCatalog/apto/catalog/backend/pages/group/group.html.twig',
                    'controller' => 'GroupController'
                ],
                '/price-matrix/' => [
                    'templateUrl' => '@AptoCatalog/apto/catalog/backend/pages/price-matrix/price-matrix.html.twig',
                    'controller' => 'PriceMatrixController'
                ],
                '/filter-property/' => [
                    'templateUrl' => '@AptoCatalog/apto/catalog/backend/pages/filter/filter-property.html.twig',
                    'controller' => 'FilterPropertyController'
                ],
                '/filter-category/' => [
                    'templateUrl' => '@AptoCatalog/apto/catalog/backend/pages/filter/filter-category.html.twig',
                    'controller' => 'FilterCategoryController'
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
            '/shop/' => [
                'route' => '/shop/',
                'label' => 'Katalog',
                'icon' => 'f1ab',
                'aclMessagesRequired' => json_encode([
                    'commands' => [],
                    'queries' => ['FindShops', 'FindCategories', 'FindProducts', 'FindGroups'],
                    'strategy' => 'one'
                ]),
                'subItems' => [
                    [
                        'route' => '/shop/',
                        'label' => 'Domains',
                        'icon' => 'f085',
                        'aclMessagesRequired' => json_encode([
                            'commands' => [],
                            'queries' => ['FindShops'],
                            'strategy' => 'all'
                        ])
                    ],
                    [
                        'route' => '/category/',
                        'label' => 'Kategorien',
                        'icon' => 'f290',
                        'aclMessagesRequired' => json_encode([
                            'commands' => [],
                            'queries' => ['FindCategories'],
                            'strategy' => 'all'
                        ])
                    ],
                    [
                        'route' => '/product/',
                        'label' => 'Produkte',
                        'icon' => 'f217',
                        'aclMessagesRequired' => json_encode([
                            'commands' => [],
                            'queries' => ['FindProducts'],
                            'strategy' => 'all'
                        ])
                    ],
                    [
                        'route' => '/group/',
                        'label' => 'Gruppen',
                        'icon' => 'f247',
                        'aclMessagesRequired' => json_encode([
                            'commands' => [],
                            'queries' => ['FindGroups'],
                            'strategy' => 'all'
                        ])
                    ],
                    [
                        'route' => '/price-matrix/',
                        'label' => 'Preismatrizen',
                        'icon' => 'f0d6',
                        'aclMessagesRequired' => json_encode([
                            'commands' => [],
                            'queries' => ['FindPriceMatricesByPage'],
                            'strategy' => 'all'
                        ])
                    ],
                    [
                        'route' => '/filter-property/',
                        'label' => 'Filter Eigenschaften',
                        'icon' => 'f0b0',
                        'aclMessagesRequired' => json_encode([
                            'commands' => [],
                            'queries' => [],
                            'strategy' => 'all'
                        ])
                    ],
                    [
                        'route' => '/filter-category/',
                        'label' => 'Filter Kategorien',
                        'icon' => 'f233',
                        'aclMessagesRequired' => json_encode([
                            'commands' => [],
                            'queries' => [],
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

