<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace Application\Controller;


use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class AuthController extends AbstractRestfulController
{
    public function indexAction()
    {
        $this->layout()->setTemplate('layout/auth');

        return new ViewModel();
    }


    public function registerAction()
    {
        $this->layout()->setTemplate('layout/auth');

        return new ViewModel();
    }

    public function recoverPasswordAction()
    {
        $this->layout()->setTemplate('layout/auth');

        return new ViewModel();
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