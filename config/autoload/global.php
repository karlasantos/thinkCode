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

use Doctrine\DBAL\Driver\PDOPgSql\Driver;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\EntityManager;
use User\Entity\User;
use Zend\Navigation\Service\DefaultNavigationFactory;

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
            //configurações para mapeamento de entidades
            'orm_default' => [
                'doctrine_type_mappings' => ['enum' => 'string'],
                'driverClass' => Driver::class,
                'params' => [
                    'host' => 'localhost',
                    'port' => '5432',
                    'driverOptions' => [
                        1002 => 'SET NAMES utf8',
                    ]
                ]
            ],
            //driver de conexão com o DB
            'driver' => [
                'my_annotation_driver' => [
                    'class' => AnnotationDriver::class,
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
        //configurações de autenticação da aplicação
        'authentication' => [
            'orm_default' => [
                'object_manager'      => EntityManager::class,
                'identity_class'      => User::class,
                'identity_property'   => 'email',
                'credential_property' => 'password',
                'credential_callable' => function(User $user, $passwordSent) {
                    return password_verify($passwordSent, $user->getPassword());
                }
            ]
        ]
    ],
    'service_manager' => [
        'factories' => [
            'navigation' => DefaultNavigationFactory::class,
        ],
    ],
];
