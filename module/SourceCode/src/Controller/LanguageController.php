<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Controller;


use Application\Controller\RestfulController;
use Doctrine\ORM\EntityManager;
use SourceCode\Model\Entity\Language;
use SourceCode\Model\Entity\SourceCode;
use SourceCode\Model\CodeBypassCommand;
use SourceCode\Model\Vertex;
use SourceCode\Service\AnalysisStructure;
use SourceCode\Service\DataCollect;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

/**
 * Class LanguageController
 * Controller de Linguagem de Programação, responsável pela listagem das linguagens cadastradas
 * @package SourceCode\Controller
 */
class LanguageController extends RestfulController
{

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager);
    }

    /**
     * Retorna todas as linguagens de programação cadastradas
     *
     * @return mixed|JsonModel
     */
    public function getList()
    {
        //página selecionada
        $search  = $this->params()->fromQuery('search');

        $languages = $this->entityManager->createQueryBuilder()
                    ->select('partial languages.{id, name}')
                    ->from(Language::class, 'languages');

        if($search) {
            $languages->andWhere('languages.name = :languageName')
                ->setParameter('languageName', $search);
        }

        $languages = $languages->getQuery()
            ->getArrayResult();

        return new JsonModel(array(
            'results' => $languages,
            'total' => count($languages),
        ));
    }

    public function get($id)
    {

    }
}