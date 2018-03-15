<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace Application\Controller\Factory;
use Application\Controller\AuthController;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

/**
 * Classe fabricadora genérica de Controller
 *
 * Class ControllerFactory
 * @package Application\Controller\Factory
 */
class ControllerFactory implements FactoryInterface
{
    /**
     * Método mágico de invocação de classe
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AuthController|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $em = $container->get(EntityManager::class);

        return new $requestedName($em);
    }
}