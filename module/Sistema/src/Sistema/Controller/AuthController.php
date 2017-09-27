<?php
/**
 * Created by PhpStorm.
 * User: karla
 * Date: 21/09/2017
 * Time: 17:03
 */

namespace Sistema\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AuthController extends AbstractActionController
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
}