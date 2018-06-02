<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 * @ignore
 */

namespace SourceCode;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Application\Controller\Factory\ControllerFactory;

/**
 * Class Module
 * Configurações do Zend para cada módulo
 * @package SourceCode
 * @ignore
 */
class Module implements ConfigProviderInterface, ServiceProviderInterface, ControllerProviderInterface
{
    const VERSION = '3.0.3-dev';

    /**
     * Retorna a configuração do módulo
     *
     * @return array|mixed|\Traversable
     * @ignore
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * Retorna as configurações de fabricação de controllers da aplicação
     *
     * @return array|\Zend\ServiceManager\Config
     * @ignore
     */
    public function getControllerConfig()
    {
        //Assinatura e fabricação dos controllers
        return [
            //atribui um aliases para os controllers fabricados
            'aliases' => [
                'language'    => Controller\LanguageController::class,
                'problem'     => Controller\ProblemController::class,
                'source-code' => Controller\SourceCodeController::class,
            ],
            'factories' => [
                Controller\LanguageController::class   => ControllerFactory::class,
                Controller\ProblemController::class    => ControllerFactory::class,
                Controller\SourceCodeController::class => ControllerFactory::class,
            ],
        ];
    }

    /**
     * Retorna as configurações de fabricação de services da aplicação
     *
     * @return array|\Zend\ServiceManager\Config
     * @ignore
     */
    public function getServiceConfig()
    {
        //Assinatura e fabricação dos services
        return [];
    }
}
