<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Controller\Factory;


use Doctrine\ORM\EntityManager;
use SourceCode\Controller\SourceCodeController;
use SourceCode\Service\DataCollect;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class SourceCodeControllerFactory implements FactoryInterface
{
    /**
     * Método mágico de invocação de classe
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $em = $container->get(EntityManager::class);
        $dataCollect = $container->get(DataCollect::class);
        return new SourceCodeController($em, $dataCollect);
    }
}