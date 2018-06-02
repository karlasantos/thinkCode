<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace Application\Controller;

use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Class ActionController
 * Define o modelo dos ActionControllers do sistema. As actions são responsáveis pelos retornos de views.
 * @package Application\Controller
 * @abstract
 */
abstract class ActionController extends AbstractActionController
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
     * ActionController constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}