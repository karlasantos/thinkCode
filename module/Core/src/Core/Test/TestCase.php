<?php
namespace Core\Test;

use Zend\Mvc\Application;
use Zend\Di\Di;
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\Mvc\MvcEvent;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * @var Zend\Mvc\Application
     */
    protected $application;
    
    /**
     * @var Zend\Di\Di
     */
    protected $di;

    public function setup()
    {
        parent::setup();

        $config = include 'config/application.config.php';
        $config['module_listener_options']['config_static_paths'] = array(getcwd() . '/config/test.config.php');

        if (file_exists(__DIR__ . '/config/test.config.php')) {
            $moduleConfig = include __DIR__ . '/config/test.config.php';
            array_unshift($config['module_listener_options']['config_static_paths'], $moduleConfig);
        }
        
        $this->serviceManager = new ServiceManager(new ServiceManagerConfig(
            isset($config['service_manager']) ? $config['service_manager'] : array()
        ));
        $this->serviceManager->setService('ApplicationConfig', $config);
        $this->serviceManager->setFactory('ServiceListener', 'Zend\Mvc\Service\ServiceListenerFactory');
        
        $moduleManager = $this->serviceManager->get('ModuleManager');
        $moduleManager->loadModules();
        $this->routes = array();
        foreach ($moduleManager->getModules() as $m) {
            $moduleConfig = __DIR__ . '/../../../../' . ucfirst($m) . '/config/module.config.php';
            if (is_file($moduleConfig)) {
                $moduleConfig = include $moduleConfig;
                if (isset($moduleConfig['router'])) {
                    foreach($moduleConfig['router']['routes'] as $key => $name) {
                        $this->routes[$key] = $name;
                    }
                }
            }
        }
        $this->serviceManager->setAllowOverride(true);

        $this->application = $this->serviceManager->get('Application');
        $this->event  = new MvcEvent();
        $this->event->setTarget($this->application);
        $this->event->setApplication($this->application)
            ->setRequest($this->application->getRequest())
            ->setResponse($this->application->getResponse())
            ->setRouter($this->serviceManager->get('Router'));

        $this->createDatabase();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->dropDatabase();
        $this->getEntityManager()->getConnection()->close();
    }

    /**
     * Retrive a EntityManager
     * 
     * @return Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->serviceManager->get('EntityManager');
    }

    /**
     * Retrieve Service
     *
     * @param  string $service
     * @return Service
     */
    protected function getService($service)
    {
        return $this->serviceManager->get($service);
    }

    /**
     * @return void
     */
    public function createDatabase()
    {
        $em = $this->getEntityManager();

        $queries = include \Bootstrap::getModulePath() . '/data/test.data.php';

        if (isset($queries['drop'])) {
            $em->getConnection()->exec($queries['drop']);
        }

        if (isset($queries['create'])) {
            $em->getConnection()->exec($queries['create']);
        }

        if (isset($queries['fixture'])) {
            $em->getConnection()->exec($queries['fixture']);
        }
    }

    /**
     * @return void
     */
    public function dropDatabase()
    {
        $em = $this->getEntityManager();

        $queries = include \Bootstrap::getModulePath() . '/data/test.data.php';

        if (isset($queries['drop'])) {
            $em->getConnection()->exec($queries['drop']);
        }
    }
}
