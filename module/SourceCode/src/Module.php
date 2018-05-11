<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace SourceCode;

use SourceCode\Controller\Factory\LanguageControllerFactory;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Application\Controller\Factory\ControllerFactory;
use Zend\ServiceManager\Factory\InvokableFactory;

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
            //atribui um aliases para os controllers fabricados
            'aliases' => [
                'language' => Controller\LanguageController::class,
                'problem' => Controller\ProblemController::class,
                'source-code' => Controller\SourceCodeController::class,
            ],
            'factories' => [
                Controller\ProblemController::class    => ControllerFactory::class,
                Controller\SourceCodeController::class => ControllerFactory::class,
                Controller\LanguageController::class   => LanguageControllerFactory::class,
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
        //Assinatura e fabricação dos services
        return [
            //atribui um aliases para os services fabricados
            'aliases' => [
                'dataCollect' => Service\DataCollect::class
            ],
            'factories' => [
                Service\DataCollect::class =>  ControllerFactory::class,
            ],
        ];
    }
}
