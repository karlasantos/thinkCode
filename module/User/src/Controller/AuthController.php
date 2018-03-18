<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace User\Controller;

use Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;

/**
 * Class AuthController
 * Classe para controlar a autenticação de usuários no sistema
 *
 * @package Application\Controller
 */
class AuthController extends AbstractRestfulController
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
     * Realiza o login do usuário
     *
     * @return \Zend\Http\Response|JsonModel|ViewModel
     */
    public function loginAction() {
        $messageError = null;
        $request = $this->getRequest();
        $urlRequest = $this->url()->fromRoute();
        $apiRequest = strpos($urlRequest, 'api');

        //verifica se o usuário está logado
        if($this->authService->hasIdentity()) {
            /* verifica origem da requisição:
                   true: requisição realizada pela API e retorno JSON
                   false: requisição realizada pelo ambiente e redireciona para a página home
               */
            return ($apiRequest)? (new JsonModel(array('message' => 'Usuário já logado'))) : ($this->redirect()->toRoute('home'));

        }

        //verificar o login do usuário
        if($request->isPost()) {
            //se dados enviados na requisição forem validados segue essa linha:
            //todo capturar os dados enviados na requisição
            $data = null;
            //passando as credenciais para o adaptador de login
            /** @var CallbackCheckAdapter $authAdapter */
            $authAdapter = $this->authService->getAdapter();
            $authAdapter->setIdentity($data['email']);
            $authAdapter->setCredential($data['password']);

            $result = $this->authService->authenticate();

            if($result->isValid()) {
                //todo definir o status da requisição para ok
                /* verifica origem da requisição:
                   true: requisição realizada pela API e retorno JSON
                   false: requisição realizada pelo ambiente e redireciona para a página home
               */
                return ($apiRequest)? (new JsonModel(array())) : ($this->redirect()->toRoute('home'));
            } else {
                $messageError = "Login Inválido";
            }
        }

        //todo define o status da requisição

        //verifica se a requisição foi realizada como API e retorna um JSON como resposta
        //todo verificar essa opção de get na API, deve ser desabilitada
        if($apiRequest) {
            return new JsonModel(
                array(
                    'error' => $messageError
                )
            );
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