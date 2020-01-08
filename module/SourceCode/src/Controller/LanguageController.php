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
use Zend\View\Model\JsonModel;

/**
 * Class LanguageController
 * Controller de Linguagem de Programação, responsável pela listagem das linguagens cadastradas
 * @package SourceCode\Controller
 */
class LanguageController extends RestfulController
{

    /**
     * LanguageController constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager);
    }

    /**
     * Retorna todas as linguagens de programação cadastradas
     *
     * @api
     * @return mixed|JsonModel
     */
    public function getList()
    {
        //página selecionada
        $search  = $this->params()->fromQuery('search');

        $language = new Language();
        $languages = $language->getList($this->entityManager, $search);

        return new JsonModel(array(
            'results' => $languages,
            'total' => count($languages),
        ));
    }
}