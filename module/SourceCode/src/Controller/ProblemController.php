<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Controller;


use Application\Controller\RestfulController;
use Application\Entity\OrderTemplate;
use SourceCode\Entity\Problem;
use User\Entity\User;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class ProblemController
 * Controler dos problemas cadastrados, responsável pelo detalhamento de um problema e pela listagem de todos os problemas
 * @package SourceCode\Controller
 */
class ProblemController extends RestfulController
{
    const PROBLEM_NOT_FOUND = 'Problema não encontrado.';

    /**
     * Retorna a interface de visualização da listagem dos problemas
     *
     * @return ViewModel
     */
    public function listAction()
    {
        return new ViewModel();
    }

    /**
     * Retorna a interface de visualização de um problema específico
     * @return ViewModel
     */
    public function viewAction()
    {
        $id  = $this->params()->fromQuery('id');
        return new ViewModel(array('id' => $id));
    }

    /**
     * Retorna todos os problemas cadastrados
     * @return mixed|JsonModel
     */
    public function getList()
    {
        //página selecionada
        $page  = $this->params()->fromQuery('page');
        //contador de problemas por página
        $count = $this->params()->fromQuery('count');
        //ordenador
        $sort  = $this->params()->fromQuery('sort');

        //prepara o template de ordenação
        $order = new OrderTemplate();
        $order->add(array(
            'title'        => 'problem.title',
            'categoryName' => 'cat.name'
        ));

        //define os parâmetros DES ou ASC
        $order->setParamsFromRoute($sort);

        $problems = $this->entityManager->createQueryBuilder()
                    ->select('problem.id, problem.title, cat.name AS categoryName')
                    ->from(Problem::class, 'problem')
                    ->leftJoin('problem.category', 'cat')
                    ->orderBy($order->getField(), $order->getMode())
                    ->getQuery()
                    ->getArrayResult();

        foreach ($problems as $key => $problem) {
            //organizar conforme o usuário logado
            $problems[$key]['resolved'] = false;
        }

        $total      = count($problems);
        $paginator  = new Paginator(new ArrayAdapter($problems));

        //define a página
        if($page)
            $paginator->setCurrentPageNumber($page);

        //define a quantidade de problemas por página
        if ($count)
            $paginator->setDefaultItemCountPerPage($count);

        return new JsonModel(
            array(
                'results' => (array) $paginator->getCurrentItems(),
                'total' => $total
            )
        );
    }

    /**
     * Retorna os dados de um problema específico
     *
     * @param mixed $id
     * @return mixed|JsonModel
     */
    public function get($id)
    {
        $id = intval($id);
        $session = $this->params()->fromQuery('session');

        try {
            $problem = $this->entityManager->createQueryBuilder()
                ->select('problem.id, problem.title, problem.description, cat.name AS categoryName, cat.description AS categoryDescription')
                ->from(Problem::class, 'problem')
                ->leftJoin('problem.category', 'cat')
                ->where('problem.id = :problemId')
                ->setParameter('problemId', $id)
                ->getQuery()
                ->getSingleResult();

            //verifica se o atributo para buscar as informações de linguagem padrão foram inseridos
            if($session) {
                $user = $this->entityManager->find(User::class,  $_SESSION['Zend_Auth']->getArrayCopy()['storage']['id']);
                if($user instanceof User) {
                    $language = $user->getProfile()->getDefaultLanguage();
                    $problem['language'] = ['id' => $language->getId(), 'name' => $language->getName()];
                    $problem['languageId'] = $problem['language']['id'];
                }
            }
        } catch (\Exception $exception) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel(
                array(
                    'result' => ProblemController::PROBLEM_NOT_FOUND,
                    'exception' => $exception->getMessage()
                )
            );
        }
        return new JsonModel(
            array(
                'result' => $problem
            )
        );
    }
}