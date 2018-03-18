<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace User\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Doctrine\ORM\EntityManager;

class UserController extends AbstractRestfulController
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function indexAction()
    {
        return new JsonModel([
            'results' => array(),
        ]);
    }

    public function getList()
    {
        return new JsonModel([
            'results' => array(),
        ]);
    }

    public function get($id)
    {
        return new JsonModel([
            'result' => array(),
        ]);
    }

    public function create($data)
    {
        return new JsonModel([
            'message' => 'Create successful'
        ]);
    }

    public function update($id, $data)
    {
        return new JsonModel([
            'message' => 'Update successful!'
        ]);
    }

    public function delete($id)
    {
        return new JsonModel([
            'message' => 'Deleted successful!'
        ]);
    }
}