<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace Application\Controller;

use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractRestfulController;

/**
 * Class RestfulController
 * Define o modelo dos RestfulControllers do sistema
 * @package Application\Controller
 * @abstract
 */
abstract class RestfulController extends AbstractRestfulController
{
    /**
     * Gerenciador de entidades do Doctrine
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Construtor da classe
     *
     * RestfulController constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}