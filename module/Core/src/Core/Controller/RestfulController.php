<?php
/**
 * XC ERP
 * @copyright Copyright (c) XC Ltda
 * @author Wagner Silveira <wagnerdevel@gmail.com>
 */
namespace Core\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\EventManager\EventManagerInterface;

class RestfulController extends AbstractRestfulController
{
    public function options()
    {
        $response = $this->getResponse();
        $headers  = $response->getHeaders();

        $headers->addHeaderLine('Allow', implode(',', array(
            'GET',
            'PATCH',
            'PUT',
            'DELETE',
        )));

        $headers->addHeaderLine('Access-Control-Allow-Origin', '*');

        return $headers;
    }

    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);

        $events->attach('dispatch', array($this, 'options'), 10);
    }

    /**
     * @return \Zend\Log\Logger
     */
    protected function logger()
    {
        return $this->getServiceLocator()->get('Logger');
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getServiceLocator()->get('EntityManager');
    }

    /**
     * @return \Zend\Session\Container
     */
    protected function getSession()
    {
        return $this->getServiceLocator()->get('Session');
    }
}
