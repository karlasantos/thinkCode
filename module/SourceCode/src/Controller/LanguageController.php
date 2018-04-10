<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Controller;


use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractRestfulController;

class LanguageController  extends AbstractRestfulController
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}