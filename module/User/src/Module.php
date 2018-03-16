<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace User;

use User\Controller\Factory\AuthControllerFactory;
use User\Service\Factory\AuthenticationServiceFactory;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Application\Controller\Factory\ControllerFactory;

/**
 * Class Module
 * Configurações do Zend para cada módulo
 * @package Application
 */
class Module implements ConfigProviderInterface, ServiceProviderInterface, ControllerProviderInterface
{
    const VERSION = '3.0.3-dev';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to seed
     * such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getControllerConfig()
    {
        //Assinatura e fabricação dos controllers
        return [
            'factories' => [
                Controller\AuthController::class  => AuthControllerFactory::class,
                Controller\UserController::class  => ControllerFactory::class,
            ],
        ];
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getServiceConfig()
    {
        // TODO: Implement getServiceConfig() method.
        return [
            'factories' => [
                AuthenticationServiceInterface::class => AuthenticationServiceFactory::class,
            ],
        ];
    }
}
