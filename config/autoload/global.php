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

return array(
    'doctrine' => array(
        'db' => array(
            'driver' => 'pdo_pgsql',
            'host'   => 'localhost',
            'port'   => '5432',
            'dbname' => 'tcc_db',
        ),
        'paths' => array(
        ),
        'isDevEnvironment' => true,
    ),
    'security' => array(
        'encryptionKey' => 'AF4ds#$fdgUUyga3sd82.fdBipFmsatZ'
    ),

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
                'route' => 'sistema',
            ),
//            array(
//                'label' =>
//                '<i class="fa fa-edit" title="Cadastros"></i>'.
//                '<span class="title">Cadastros</span>'.
//                '<span class="arrow"></span>',
//                // 'route' => 'person',
//                'uri' => '#',
//                'pages' => array(
//                    array(
//                        'label' => 'Cliente',
//                        'uri' => '#/pessoa/cliente'
//                    ),
//                    array(
//                        'label' => 'Fornecedor',
//                        'uri' => '#/pessoa/fornecedor'
//                    ),
//                    array(
//                        'label' => 'Transportadora',
//                        'uri' => '#/pessoa/transportadora'
//                    ),
//                    array(
//                        'label' => 'Colaborador',
//                        'uri' => '#/pessoa/colaborador'
//                    ),
//                    array(
//                        'label' => 'Unidade de Negócio',
//                        'uri' => '#/pessoa/unidade-negocio'
//                    ),
//                    array(
//                        'label' =>
//                        '<i class="fa fa-plus"></i>'.
//                        '<span class="title">Mais</span>'.
//                        '<span class="arrow"></span>',
//                        // '<span class="badge badge-disable pull-right">0</span>',
//                        'uri' => '#',
//                        'pages' => array(
//                            array(
//                                'label' => 'Profissão',
//                                'uri' => '#/pessoa/profissao'
//                            ),
//                            array(
//                                'label' => 'Ramo de Atividade',
//                                'uri' => '#/pessoa/ramo-atividade'
//                            ),
//                            array(
//                                'label' => 'Departamento',
//                                'uri' => '#/pessoa/departamento'
//                            ),
//                            array(
//                                'label' => 'Estados e Cidades',
//                                'uri' => '#/pessoa/localizacao'
//                            ),
//                            array(
//                                'label' => 'Veículos',
//                                'uri' => '#/pessoa/veiculo'
//                            ),
//                        ),
//                    ),
//                ),
//            ),
//            array(
//                'label' =>
//                '<i class="fa fa-shopping-cart" title="Produtos"></i>'.
//                '<span class="title">Produtos</span>'.
//                '<span class="arrow"></span>',
//                // '<span class="badge badge-disable pull-right">0</span>',
//                'uri' => '#',
//                'pages' => array(
//                    array(
//                        'label' => 'Produtos',
//                        'uri' => '#/produto/produto'
//                    ),
//                    array(
//                        'label' => 'Marcas e Modelos',
//                        'uri' => '#/produto/marca'
//                    ),
//                    array(
//                        'label' => 'NCM',
//                        'uri' => '#/produto/ncm'
//                    ),
//                    array(
//                        'label' =>
//                        '<i class="fa fa-plus"></i>'.
//                        '<span class="title">Mais</span>'.
//                        '<span class="arrow"></span>',
//                        // '<span class="badge badge-disable pull-right">0</span>',
//                        'route' => 'produto',
//                        'pages' => array(
//                            array(
//                                'label' => 'Unidade de medida',
//                                'uri' => '#/produto/unidade-medida'
//                            ),
//                            array(
//                                'label' => 'Categorias',
//                                'uri' => '#/produto/categoria'
//                            ),
//                            array(
//                                'label' => 'Tipos de Venda',
//                                'uri' => '#/produto/tipo-venda'
//                            ),
//                            array(
//                                'label' => 'Características',
//                                'uri' => '#/produto/caracteristica'
//                            ),
//                        ),
//                    ),
//                ),
//            ),
//            array(
//                'label' =>
//                '<i class="fa fa-cubes" title="Estoque"></i>'.
//                '<span class="title">Estoque</span>'.
//                '<span class="arrow"></span>',
//                'uri' => '#',
//                'pages' => array(
//                    array(
//                        'label' => 'Estoque',
//                        'uri' => '#/estoque/estoque'
//                    ),
//                    array(
//                        'label' => 'Movimentações',
//                        'uri' => '#/estoque/movimentacoes'
//                    ),
//                    array(
//                        'label' => 'Locais de Estoque',
//                        'uri' => '#/estoque/locais',
//                    )
//                )
//            ),
//            //            array(
//            //                'label' =>
//            //                '<i class="icon-custom-form"></i>'.
//            //                '<span class="title">Pedidos</span>'.
//            //                '<span class="badge badge-disable pull-right">0</span>',
//            //                'route' => 'pedido',
//            //                'pages' => array(
//            //                    array(
//            //                        'label' => 'Pedidos',
//            //                        'route' => 'pedido',
//            //                        'controller' => 'pedido',
//            //                    )
//            //                ),
//            //            ),
//            array(
//                'label' =>
//                '<i class="fa fa-line-chart" title="Finanças"></i>'.
//                '<span class="title">Finanças</span>'.
//                '<span class="arrow"></span>',
//                'uri' => '#',
//                'pages' => array(
//                    array(
//                        'label' => 'Lançamentos',
//                        'uri' => '#/financeiro/lancamento'
//                    ),
//                    array(
//                        'label' => 'Lançamentos Recorrentes',
//                        'uri' => '#/financeiro/lancamento-recorrente'
//                    ),
//                    array(
//                        'label' => 'Contas',
//                        'uri' => '#/financeiro/conta'
//                    ),
//                    array(
//                        'label' => 'Cartões',
//                        'uri' => '#/financeiro/cartao'
//                    ),
//                    array(
//                        'label' => 'Transferências',
//                        'uri' => '#/financeiro/transferencia'
//                    ),
//                    array(
//                        'label' => 'Plano de Contas',
//                        'uri' => '#/financeiro/plano-contas'
//                    ),
//                    array(
//                        'label' => 'Provisionamento FOPAG',
//                        'uri' => '#/financeiro/custos'
//                    ),
//                    array(
//                        'label' => 'Boletos Emitidos',
//                        'uri' => '#/boleto/boleto'
//                    ),
////                    array(
////                        'label' => 'Remessas/Retornos',
////                        'uri' => '#/financeiro/remessa'
////                    ),
//                ),
//            ),
//            array(
//                'label' =>
//                '<i class="fa fa-dollar" title="Fiscal"></i>'.
//                '<span class="title">Fiscal</span>'.
//                '<span class="arrow"></span>',
//                'uri' => '#',
//                'pages' => array(
//                    array(
//                        'label' => 'Natureza de Operação',
//                        'uri' => '#/fiscal/natureza-operacao'
//                    ),
//                    array(
//                        'label' => 'NF-e',
//                        'uri' => '#/fiscal/nfe'
//                    ),
//                    array(
//                        'label' => 'NFS-e',
//                        'uri' => '#/fiscal/nfse'
//                    ),
//                    array(
//                        'label' => 'Faixas Inutilizadas',
//                        'uri' => '#/fiscal/inutiliza'
//                    ),
//                ),
//            ),
//            array(
//                'label' =>
//                '<i class="fa fa-cart-arrow-down" title="Compras"></i>'.
//                '<span class="title">Compras</span>'.
//                '<span class="arrow"></span>',
//                'uri' => '#',
//                'pages' => array(
//                    array(
//                        'label' => 'Pedidos de Compra',
//                        'uri' => '#/pedido/compra'
//                    ),
//                    array(
//                        'label' => 'Pedidos de Serviço',
//                        'uri' => '#/pedido/servico'
//                    )
//                ),
//            ),
//            array(
//                'label' =>
//                '<i class="fa fa-cart-plus" title="Vendas"></i>'.
//                '<span class="title">Vendas</span>'.
//                '<span class="arrow"></span>',
//                'uri' => '#',
//                'pages' => array(
//                    array(
//                        'label' => '<i class="fa fa-shopping-cart"></i> Pedidos de Venda',
//                        'uri' => '#/venda/pedido'
//                    ),
//                    array(
//                        'label' => '<i class="fa fa-file-text-o"></i> Orçamentos',
//                        'uri' => '#/venda/orcamento'
//                    ),
//                    array(
//                        'label' => '<i class="fa fa-newspaper-o"></i> Modelo de Propostas',
//                        'uri' => '#/venda/modelo-propostas',
//                    ),
//                    // array(
//                    //     'label' => '<i class="fa fa-shopping-basket"></i> PDVs',
//                    //     'uri' => '#/venda/pdvs'
//                    // ),
//                    array(
//                        'label' =>
//                            '<i class="fa fa-plus"></i>'.
//                            '<span class="title">Mais</span>'.
//                            '<span class="arrow"></span>',
//                        // '<span class="badge badge-disable pull-right">0</span>',
//                        'route' => 'venda',
//                        'pages' => array(
//                            array(
//                                'label' => 'Prioridades',
//                                'uri' => '#/venda/prioridade'
//                            ),
//                        ),
//                    ),
//                ),
//            ),
//            array(
//                'label' =>
//                '<i class="fa fa-wrench" title="Serviços"></i>'.
//                '<span class="title">Serviços</span>'.
//                '<span class="arrow"></span>',
//                'uri' => '#',
//                'pages' => array(
//                    array(
//                        'label' => '<i class="fa fa-file-text-o"></i> Contrato',
//                        'uri' => '#/servico/contrato',
//                    ),
//                    array(
//                        'label' => '<i class="fa fa-file-text-o"></i> Orçamentos',
//                        'uri' => '#/venda/orcamento'
//                    ),
//                    array(
//                        'label' => '<i class="fa fa-tags"></i> Ordem de Serviço',
//                        'uri' => '#/servico/ordem-servico',
//                    ),
//                    array(
//                        'label' => '<i class="fa fa-cog"></i> Serviço',
//                        'uri' => '#/servico/servico',
//                    ),
//                    array(
//                        'label' => '<i class="fa fa-table"></i> Tabela de Serviço',
//                        'uri' => '#/servico/tabela-servico',
//                    ),
//                    array(
//                        'label' => '<i class="fa fa-newspaper-o"></i> Modelo de Cláusulas',
//                        'uri' => '#/servico/modelo-clausulas',
//                    ),
//                    array(
//                        'label' => '<i class="fa fa-newspaper-o"></i> Modelo de Propostas',
//                        'uri' => '#/venda/modelo-propostas',
//                    ),
//                    array(
//                        'label' =>
//                            '<i class="fa fa-plus"></i>'.
//                            '<span class="title">Mais</span>'.
//                            '<span class="arrow"></span>',
//                        // '<span class="badge badge-disable pull-right">0</span>',
//                        'route' => 'servico',
//                        'pages' => array(
//                            array(
//                                'label' => 'Prioridades',
//                                'uri' => '#/venda/prioridade'
//                            ),
//                        ),
//                    ),
//                ),
//            ),
//            array(
//                'label' =>
//                '<i class="fa fa-puzzle-piece" title="CRM"></i>'.
//                '<span class="title">CRM</span>'.
//                '<span class="arrow"></span>',
//                'uri' => '#',
//                'pages' => array(
//                    array(
//                        'label' => '<i class="fa fa-user-plus"></i> Lead',
//                        'uri' => '#/crm/lead'
//                    ),
//                    // array(
//                    //     'label' => 'Negociações',
//                    //     'uri' => '#/crm/negociacao',
//                    // ),
//                    array(
//                        'label' => '<i class="fa fa-comments-o"></i> Relacionamentos',
//                        'uri' => '#/crm/relacionamento'
//                    ),
//                )
//            ),
//            array(
//                'label' =>
//                    '<i class="fa fa-clipboard" title="Relatórios"></i>'.
//                    '<span class="title">Relatórios</span>'.
//                    '<span class="arrow"></span>',
//                'uri' => '#',
//                'pages' => array(
//                    array(
//                        'label' => '<i class="fa fa-edit"></i> Cadastros',
//                        'uri' => '#/relatorios/pessoa'
//                    ),
//                    array(
//                        'label' => '<i class="fa fa-shopping-cart"></i> Produtos',
//                        'uri' => '#/relatorios/produto'
//                    ),
//                    array(
//                        'label' => '<i class="fa fa-cubes"></i> Estoque',
//                        'uri' => '#/relatorios/estoque'
//                    ),
//                    array(
//                        'label' => '<i class="fa fa-line-chart"></i> Finanças',
//                        'uri' => '#/relatorios/financeiro'
//                    ),
//                    array(
//                        'label' => '<i class="fa fa-dollar"></i> Fiscal',
//                        'uri' => '#/relatorios/fiscal'
//                    ),
//                    array(
//                        'label' => '<i class="fa fa-cart-arrow-down"></i> Compras',
//                        'uri' => '#/relatorios/compra'
//                    ),
//                    array(
//                        'label' => '<i class="fa fa-cart-plus"></i> Vendas',
//                        'uri' => '#/relatorios/venda'
//                    ),
//                    array(
//                        'label' => '<i class="fa fa-wrench"></i> Serviços',
//                        'uri' => '#/relatorios/servico'
//                    ),
//                    array(
//                        'label' => '<i class="fa fa-puzzle-piece"></i> CRM',
//                        'uri' => '#/relatorios/crm'
//                    ),
//                    array(
//                        'label' => '<i class="fa fa-wrench"></i> Sistema',
//                        'uri' => '#/relatorios/sistema'
//                    )
//                ),
//            ),
//            array(
//                'label' =>
//                '<i class="fa fa-desktop" title="Sistema"></i>'.
//                '<span class="title">Sistema</span>'.
//                '<span class="arrow"></span>',
//                'uri' => '#',
//                'pages' => array(
//                    array(
//                        'label' => '<i class="fa fa-user"></i> Usuários',
//                        'uri' => '#/sistema/user'
//                    ),
//                    //                    array(
//                    //                        'label' => '<i class="fa fa-calendar"></i> Calendário',
//                    //                        'uri' => '#/sistema/calendar'
//                    //                    ),
//                    array(
//                        'label' => '<i class="fa fa-group"></i> Grupos',
//                        'uri' => '#/sistema/grupo'
//                    ),
//                    array(
//                        'label' => '<i class="fa fa-share-square-o"></i> Sessão',
//                        'uri' => '#/sistema/sessao'
//                    )
//                ),
//            ),
//            array(
//                'label' =>
//                '<i class="fa fa-cogs" title="Configurações"></i>'.
//                '<span class="title">Configurações</span>'.
//                '<span class="arrow"></span>',
//                'uri' => '#',
//                'pages' => array(
//                    array(
//                        'label' => '<i class="fa fa-envelope"></i> Email',
//                        'uri' => '#/configuracao/email'
//                    ),
//                    array(
//                        'label' => '<i class="fa fa-barcode"></i> Boleto',
//                        'uri' => '#/configuracao/boleto'
//                    )
//                )
//            )
        ),
    ),
);
