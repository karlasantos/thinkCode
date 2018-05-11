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
use Zend\Mvc\Controller\AbstractRestfulController;
use Doctrine\ORM\EntityManager;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ProblemController extends RestfulController
{
    const PROBLEM_NOT_FOUND = 'Problema não encontrado.';

    public function listAction()
    {
        return new ViewModel();
    }

    public function viewAction()
    {
        $id  = $this->params()->fromQuery('id');
        return new ViewModel(array('id' => $id));
    }

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
                'problems' => (array) $paginator->getCurrentItems(),
                'total' => $total
            )
        );
    }

    public function get($id)
    {
        $id = intval($id);
        try {
            $problem = $this->entityManager->createQueryBuilder()
                ->select('problem.id, problem.title, problem.description, cat.name AS categoryName, cat.description AS categoryDescription')
                ->from(Problem::class, 'problem')
                ->leftJoin('problem.category', 'cat')
                ->where('problem.id = :problemId')
                ->setParameter('problemId', $id)
                ->getQuery()
                ->getSingleResult();
        } catch (\Exception $exception) {
            return new JsonModel(
                array(
                    'result' => ProblemController::PROBLEM_NOT_FOUND,
                    'exception' => $exception->getMessage()
                )
            );
        }
        return new JsonModel(
            array(
                'problem' => $problem
            )
        );
    }
}