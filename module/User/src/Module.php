<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace User;

use User\Controller\AuthController;
use User\Controller\Factory\AuthControllerFactory;
use User\Service\Factory\AuthenticationServiceFactory;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Authentication\AuthenticationService;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Application\Controller\Factory\ControllerFactory;
use Zend\Mvc\MvcEvent;

/**
 * Class Module
 * Configurações do Zend para cada módulo
 * @package Application
 */
class Module implements ConfigProviderInterface, ServiceProviderInterface, ControllerProviderInterface
{
    const VERSION = '3.0.3-dev';

    /**
     * Verifica a permissão de acesso a determinada área do sistema e redireciona para o login em caso de não permissão
     * @param MvcEvent $event
     */
    public function onBootstrap(MvcEvent $event)
    {
        $eventManager = $event->getApplication()->getEventManager();
        $container = $event->getApplication()->getServiceManager();

        $eventManager->attach(MvcEvent::EVENT_DISPATCH,
            function(MvcEvent $event) use($container) {
                $match = $event->getRouteMatch();

                $authService = $container->get(AuthenticationServiceInterface::class);

                //pega a requisição acessada pelo nome da rota
                $routeName = $match->getMatchedRouteName();
                if($authService->hasIdentity()) {
                    //se usuário logado apenas retorna
                    return;
                } else if(strpos($routeName, 'tcc') !== false) {
                    //se a rota contiver o nome do módulo e usuário não estiver logado redireciona para o login
                    $match->setParam('controller', AuthController::class)
                        ->setParam('action', 'login');
                }
        }, 100);
    }

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
                'auth' => Controller\AuthController::class,
                'user' => Controller\UserController::class
            ],
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
            //atribui um aliases para os services fabricados
            'aliases' => [
                AuthenticationService::class => AuthenticationServiceInterface::class
            ],
            'factories' => [
                AuthenticationServiceInterface::class => AuthenticationServiceFactory::class,
            ],
        ];
    }
}
