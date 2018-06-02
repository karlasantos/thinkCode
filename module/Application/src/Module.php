<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 * @ignore
 */

namespace Application;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ServiceManager\Factory\InvokableFactory;
use Application\Controller\Factory\ControllerFactory;

/**
 * Class Module
 * Configurações do Zend para cada módulo
 * @package Application
 * @ignore
 */
class Module implements ConfigProviderInterface, ServiceProviderInterface, ControllerProviderInterface
{
    const VERSION = '3.0.3-dev';

    /**
     * Retorna as configurações do módulo
     *
     * @return array|mixed|\Traversable
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     *  Retorna as configurações de fabricação de controllers da aplicação
     * @ignore
     * @return array|\Zend\ServiceManager\Config
     */
    public function getControllerConfig()
    {
        //Assinatura e fabricação dos controllers
        return [
            'factories' => [
                Controller\IndexController::class => InvokableFactory::class,
            ],
        ];
    }

    /**
     *  Retorna as configurações de fabricação de services da aplicação
     * @ignore
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getServiceConfig()
    {
        return [];
    }
}
