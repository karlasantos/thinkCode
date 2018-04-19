<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Service;
use Doctrine\ORM\EntityManager;

/**
 * Class AnalysisStructure
 * Realiza a análise dos códigos fonte
 * @package SourceCode\Service
 */
class AnalysisStructure
{
    protected $entityManager;

    protected $dataCollect;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->dataCollect = new DataCollect($entityManager);
    }

    public function definirVertices()
    {
        $node = array(

        );

    }

}