<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode;

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
            'source_code_annotation_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Entity'
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => 'source_code_annotation_driver'
                ],
            ],
        ],
    ],
    //rotas de navegação
    'router' => [
        'routes' => [
            'tcc-source-code' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/source-code',
                    'defaults' => array(
                        //todo colocar o controller padrão desse módulo
                        'controller' => Controller\LanguageController::class,
                    ),
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'language' => [
                        'type' => 'segment',
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
            //rotas para a API
            'api' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/api',
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'tcc-source-code' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => '/source-code[/:controller[/:action[/:id]]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'         => '[0-9]*',
                            ],
                            'defaults' => [
                                //todo colocar o controller padrão desse módulo
                                'controller' => Controller\UserController::class
                            ],
                        ]
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
