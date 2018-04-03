<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace User;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\Router\Http\Method;
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
            'tcc-user' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/user',
                    'defaults' => array(
                        'controller' => Controller\UserController::class,
                    ),
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'user' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '[/:controller[/:action[/:id]]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'         => '[0-9]*',
                            ],
                        ]
                    ],
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
            'logout' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/logout',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'logout',
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
            //rota pública para a criação de um novo usuário
            'api-register-user' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/api/register-user',
                    'defaults' => [
                        'controller' => Controller\UserController::class,
                    ],
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'register-user' => [
                        'type' => Method::class,
                        'options' => [
                            'verb' => 'post',
                            'defaults' => [
                                'controller' => Controller\UserController::class,
                            ],
                        ]
                    ],
                ],
            ],
            //rotas para a API
            'api' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/api',
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'tcc-user' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/user[/:controller[/:action[/:id]]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'         => '[0-9]*',
                            ],
                            'defaults' => [
                                'controller' => Controller\UserController::class
                            ],
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
                    'logout' => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/logout',
                            'defaults' => [
                                'controller' => Controller\AuthController::class,
                                'action'     => 'logout',
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
