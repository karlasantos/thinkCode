<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;

/**
 * Class AuthController
 * Classe para controlar a autenticação de usuários no sistema
 *
 * @package Application\Controller
 */
class AuthController extends AbstractActionController
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

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

    public function loginAction() {

    }

    public function logoutAction() {

    }
}