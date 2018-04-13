<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Service;

use Doctrine\ORM\EntityManager;

/**
 * Class DataCollect
 * Realiza a coleta dos dados necessários para a análise
 * @package SourceCode\Service
 */
class DataCollect
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

}