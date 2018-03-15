<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return [
    //Nagegação do Menu
    'navigation' => array(
        // The DefaultNavigationFactory we configured in (1) uses 'default' as the sitemap key
        'default' => array(
            // And finally, here is where we define our page hierarchy
            array(
                'label' => '<i class="icon-home"></i> Home',
                'class' => 'start',
                'route' => 'home',
            ),
            array(
                'label' => '<i class="icon-interface-windows"></i> Sistema',
                'class' => '',
                'route' => 'application',
            ),
        ),
    ),
    //Configurações da conexão com o DB
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'doctrine_type_mappings' => ['enum' => 'string'],
                'driverClass' => 'Doctrine\DBAL\Driver\PDOPgSql\Driver',
                'params' => [
                    'host' => 'localhost',
                    'port' => '5432',
                    'driverOptions' => [
                        1002 => 'SET NAMES utf8',
                    ]
                ]
            ],
            'driver' => [
                'my_annotation_driver' => [
                    'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                    'cache' => 'array',
                    'paths' => [
                        __DIR__ . '/../../module/Application/src/Entity',
                    ],
                ],
                'orm_default' => [
                    'drivers' => [
                        // register `my_annotation_driver` for any entity under namespace `My\Namespace`
                        __NAMESPACE__ . '\Entity' => 'my_annotation_driver'
                    ]
                ]
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            'navigation' => Zend\Navigation\Service\DefaultNavigationFactory::class,
        ],
    ],
];
