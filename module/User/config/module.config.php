<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace User;

use Application\Controller\Factory\ControllerFactory;
use function PHPSTORM_META\type;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

/**
 * Configurações do módulo Application
 */
return [
    //cadastro de notação da doctrine para identificação das entidades
    'doctrine' => [
        'driver' => [
            'user_annotation_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Entity'
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => 'user_annotation_driver'
                ],
            ],
        ],
    ],
    //rotas de navegação
    'router' => [
        'routes' => [
            //rotas para a aplicação
            'user' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/',
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'user' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => '/user/[:controller[/:action[/:id]]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'         => '[0-9]*',
                            ],
                            'defaults' => array(
                                'controller' => Controller\UserController::class
                            ),
                        ]
                    ],
                    'login' => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => 'login',
                            'defaults' => [
                                'controller' => Controller\AuthController::class,
                                'action'     => 'login',
                            ],
                        ],
                    ],
                    'register' => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => 'register',
                            'defaults' => [
                                'controller' => Controller\AuthController::class,
                                'action'     => 'register',
                            ],
                        ],
                    ],
                    'recover-password' => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => 'recover-password',
                            'defaults' => [
                                'controller' => Controller\AuthController::class,
                                'action'     => 'recover-password',
                            ],
                        ],
                    ],
                ]
            ],
            //rotas para a API
            'api' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/api',
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'user' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => '/user/[:controller[/:action[/:id]]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'         => '[0-9]*',
                            ],
                            'defaults' => array(
                                'controller' => Controller\UserController::class
                            ),
                        ]
                    ],
                    'login' => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/login',
                            'defaults' => [
                                'controller' => Controller\AuthController::class,
                                'action'     => 'login',
                            ],
                        ],
                    ],
                    'register' => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/register',
                            'defaults' => [
                                'controller' => Controller\AuthController::class,
                                'action'     => 'register',
                            ],
                        ],
                    ],
                    'recover-password' => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/recover-password',
                            'defaults' => [
                                'controller' => Controller\AuthController::class,
                                'action'     => 'recover-password',
                            ],
                        ],
                    ],
                ]
            ]
        ],
    ],
    //Configurações dos arquivos de visualização
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        //Habilita a serialização Json (para utilização de API-REST)
        'strategies' => [
            'ViewJsonStrategy'
        ],
    ],
];
