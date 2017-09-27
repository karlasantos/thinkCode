<?php
namespace Core\Permission;

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class Builder implements ServiceManagerAwareInterface
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @param ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * Retrieve serviceManager instance
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    public function isAllowed($moduleName, $controllerName, $actionName, $permissions)
    {
        $em = $this->getServiceManager()->get('EntityManager');

        $resource = $em->createQueryBuilder()
            ->select('ctrl.id, act.permission, act.isPublic, mod.id as moduleId')
            ->from('Sistema\Entity\Resource', 'act')
            ->leftJoin('act.parent', 'ctrl')
            ->leftJoin('act.module', 'mod')
            ->where('mod.name = :modName')
            ->andWhere('ctrl.name = :ctrlName')
            ->andWhere('act.name = :actName')
            ->setParameter('modName', $moduleName)
            ->setParameter('ctrlName', $controllerName)
            ->setParameter('actName', $actionName)
            ->getQuery()
            ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
            ->getArrayResult();

        // nao localizou o recurso
        if (! $resource) {
            // die('resource: '. $moduleName .' - '. $controllerName .' - '. $actionName ."<br/>\n");
            return false;
        }

        $resource = $resource[0];

        if ($resource['isPublic']) {
            return true;
        }

        // sem permissao para acessar o controller
        if (! isset($permissions[$resource['moduleId']][$resource['id']])) {
            // die('controller: '. $controllerName ."<br/>\n");
            return false;
        }

        // sem permissao para acessar a action
        if (($permissions[$resource['moduleId']][$resource['id']] & $resource['permission']) != $resource['permission']) {
            // die('action: '. $actionName ."<br/>\n");
            return false;
        }

        return true;
    }
}
