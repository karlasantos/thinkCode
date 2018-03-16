<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace User\Controller;

use Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter;
use Zend\Authentication\AuthenticationServiceInterface;
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
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var AuthenticationServiceInterface
     */
    private $authService;

    /**
     * AuthController constructor.
     * @param EntityManager $entityManager
     * @param AuthenticationServiceInterface $authService
     */
    public function __construct(EntityManager $entityManager, AuthenticationServiceInterface $authService)
    {
        $this->entityManager = $entityManager;
        $this->authService = $authService;
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

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function loginAction() {
        //redireciona para o home caso o usuário já esteja logado
        if($this->authService->hasIdentity()) {
            return $this->redirect()->toRoute('home');
        }

        $messageError = null;
        $request = $this->getRequest();

        if($request->isPost()) {
            //verificar o login do usuário

            //se dados enviados na requisição forem validados segue essa linha:
            $data = null;
            //passando as credenciais para o adaptador de login
            /** @var CallbackCheckAdapter $authAdapter */
            $authAdapter = $this->authService->getAdapter();
            $authAdapter->setIdentity($data['email']);
            $authAdapter->setCredential($data['password']);

            $result = $this->authService->authenticate();

            //redireciona a página para o home se login for válido
            if($result->isValid()) {
               return $this->redirect()->toRoute('home');
            } else {
                $messageError = "Login Inválido";
            }
        }

        $this->layout()->setTemplate('layout/auth');
        return new ViewModel(
            array(
                'error' => $messageError
            )
        );
    }

    /**
     * Realiza o logout do usuário
     *
     * @return \Zend\Http\Response
     */
    public function logoutAction() {
        //destrói a sessão do usuário
        $this->authService->clearIdentity();

        return $this->redirect()->toRoute('login');
    }
}