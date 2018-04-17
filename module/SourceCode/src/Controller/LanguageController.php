<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Controller;


use Doctrine\ORM\EntityManager;
use SourceCode\Entity\SourceCode;
use SourceCode\Service\DataCollect;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class LanguageController  extends AbstractRestfulController
{
    protected $entityManager;

    /**
     * @var DataCollect
     */
    private $dataCollect;

    public function __construct(EntityManager $entityManager, DataCollect $dataCollect)
    {
        $this->entityManager = $entityManager;
        $this->dataCollect = $dataCollect;
    }

    public function getList()
    {
        $result = $this->dataCollect->getDataFromCode(new SourceCode());

        return new JsonModel([
            'resultsL' => array($result),
        ]);
    }
}