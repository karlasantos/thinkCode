<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 * @ignore
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
 * @package User
 * @ignore
 */
class Module implements ConfigProviderInterface, ServiceProviderInterface, ControllerProviderInterface
{
    const VERSION = '3.0.3-dev';

    /**
     * Verifica a permissão de acesso a determinada área do sistema e redireciona para o login em caso de não permissão
     * @param MvcEvent $event
     * @ignore
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

    /**
     * Retorna as configurações do módulo
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
     * Retorna as configurações de fabricação de services da aplicação
     *
     * @return array|\Zend\ServiceManager\Config
     * @ignore
     */
    public function getServiceConfig()
    {
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
