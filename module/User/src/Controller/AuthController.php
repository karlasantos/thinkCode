<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace User\Controller;

use User\Entity\User;
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

    const EMAIL_SENDER = "karladslencina@aluno.santoangelo.uri.br";

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

    /**
     * Gera uma senha aleatória
     *
     * @param int $length
     * @param bool $useUppercases
     * @param bool $useNumbers
     * @param bool $useSymbols
     * @return string
     */
    private function generatePassword($length = 6, $useUppercases = true, $useNumbers = true, $useSymbols = false)
    {
        $smallLetters = 'abcdefghijklmnopqrstuvwxyz';
        $capitalLetters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '1234567890';
        $symbols = '!@#$%*-';
        $password = '';
        $characters = '';
        $characters .= $smallLetters;
        if ($useUppercases) $characters .= $capitalLetters;
        if ($useNumbers) $characters .= $numbers;
        if ($useSymbols) $characters .= $symbols;
        $lengthCharacters = strlen($characters);
        for ($i = 1; $i <= $length; $i++) {
            $rand = mt_rand(1, $lengthCharacters);
            $password .= $characters[$rand-1];
        }
        return $password;
    }

    public function registerAction()
    {
        $this->layout()->setTemplate('layout/auth');

        return new ViewModel();
    }

    public function recoverPasswordAction()
    {
        $messageError = null;
        $request = $this->getRequest();
        $urlRequest = $this->url()->fromRoute();
        $apiRequest = strpos($urlRequest, 'api');

        if($request->isPost()) {
            //recupera os dados da requisição
            $data = $this->params()->fromPost();

            if(isset($data['email'])) {
                //realiza um select no DB para obter a informação de conta ativa do usuário
                $user = $this->entityManager->createQueryBuilder()
                    ->select('u.id, u.activeAccount')
                    ->from(User::class, 'u')
                    ->where('u.email like :email')
                    ->setParameter('email', $data['email'])->getQuery()->getArrayResult();
                $user = count($user) > 0 ? $user[0] : array();

                if(isset($user['activeAccount'])) {
                    $newPassword = $this->generatePassword();

                    //todo continuar aqui
                } else {
                    $this->getResponse()->setStatusCode(400);
                    $messageError = "Não foi encontrada nenhuma conta cadastrada para este e-mail.";
                }
            }

        }

        $this->layout()->setTemplate('layout/auth');

        return new ViewModel();
    }

    /**
     * Realiza o login do usuário
     *
     * @return \Zend\Http\Response|JsonModel|ViewModel
     */
    public function loginAction()
    {
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
            //recupera os dados da requisição
            $data = $this->params()->fromPost();

            //verifica se os dados foram enviados corretamente (precaução para API)
            if(isset($data['email']) && isset($data['password'])) {
                //realiza um select no DB para obter a informação de conta ativa do usuário
                $user = $this->entityManager->createQueryBuilder()
                    ->select('u.id, u.activeAccount')
                    ->from(User::class, 'u')
                    ->where('u.email like :email')
                    ->setParameter('email', $data['email'])->getQuery()->getArrayResult();
                $user = count($user) > 0 ? $user[0] : array();

                //passando as credenciais para o adaptador de login
                /* @var CallbackCheckAdapter $authAdapter */
                $authAdapter = $this->authService->getAdapter();
                $authAdapter->setIdentity($data['email']);
                $authAdapter->setCredential($data['password']);

                $result = $this->authService->authenticate();

                //se login válido e conta de usuário ativa retorna sucesso
                if ($result->isValid() && isset($user['activeAccount']) && $user['activeAccount']) {
                    $this->getResponse()->setStatusCode(200);
                    /* verifica origem da requisição:
                       true: requisição realizada pela API e retorno JSON
                       false: requisição realizada pelo ambiente e redireciona para a página home
                   */
                    return ($apiRequest) ? (new JsonModel(array('message' => 'Login realizado com sucesso.'))) : ($this->redirect()->toRoute('home'));
                } else {
                    $this->authService->clearIdentity();

                    $this->getResponse()->setStatusCode(400);
                    $messageError = "Login Inválido";
                }
            } else {
                $this->getResponse()->setStatusCode(400);
                $messageError = "Informe o email e a senha para realizar o login.";
            }
        }

        $result = !empty($messageError)? array('error' => $messageError) : array();

        //verifica se a requisição foi realizada como API e retorna um JSON como resposta
        if($apiRequest) {
            return new JsonModel($result);
        }

        $this->layout()->setTemplate('layout/auth');
        return new ViewModel(
            array($result)
        );
    }

    /**
     * Realiza o logout do usuário
     *
     * @return \Zend\Http\Response|JsonModel
     */
    public function logoutAction()
    {
        $urlRequest = $this->url()->fromRoute();
        $apiRequest = strpos($urlRequest, 'api');

        //destrói a sessão do usuário
        $this->authService->clearIdentity();

        return ($apiRequest)? new JsonModel(array('message' => 'Logout realizado com sucesso.')) : $this->redirect()->toRoute('login');
    }
}