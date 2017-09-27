<?php
namespace Core\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Core\Db\TableGateway;

class ActionController extends AbstractActionController
{
	/**
     * Returns a TableGateway
     *
     * @param  string $table
     * @return TableGateway
     */
	protected function getTable($table)
    {
        $sm = $this->getServiceLocator();
        
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
        $tableGateway = new TableGateway($dbAdapter, $table, new $table);
        $tableGateway->initialize();
        
        return $tableGateway;
    }

    /**
     * Retrive a EntityManager
     * 
     * @return Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        $sm = $this->getServiceLocator();

        return $sm->get('EntityManager');
    }

    /**
     * Returns a Service
     *
     * @param  string $service
     * @return Service
     */
    protected function getService($service)
    {
        return $this->getServiceLocator()->get($service);
    }
}