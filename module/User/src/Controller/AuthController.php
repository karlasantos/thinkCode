<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace User\Controller;

use Application\Controller\RestfulController;
use Exception;
use User\Model\Entity\User;
use Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;

/**
 * Class AuthController
 * Controller de Autenticação de usuários no sistema: responsável pelas requisições de login, logout,
 * recuperação de senha e carregamento de views de autenticação
 * @package Application\Controller
 */
class AuthController extends RestfulController
{
    /**
     * Serviço de autenticação
     *
     * @var AuthenticationServiceInterface
     */
    private $authService;

    /**
     * E-mail do remetente
     */
    const EMAIL_SENDER = "karlacc.uri@gmail.com";

    /**
     * Senha do e-mail
     */
    const PASS_EMAIL_SENDER = "uricienciadacomputacao2018";

    /**
     * AuthController constructor.
     * @param EntityManager $entityManager
     * @param AuthenticationServiceInterface $authService
     */
    public function __construct(EntityManager $entityManager, AuthenticationServiceInterface $authService)
    {
        parent::__construct($entityManager);
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
    private function generatePassword($length = 8, $useUppercases = true, $useNumbers = true, $useSymbols = false)
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

    /**
     * Carrega a página HTML de registro de nova conta no sistema
     *
     * @return ViewModel
     */
    public function registerAction()
    {
        $this->layout()->setTemplate('layout/auth');

        return new ViewModel();
    }

    /**
     * Requisição POST: Realiza a recuperação de senha de usuário enviando uma nova senha por email
     * Requisição GET: carrega a página HTML de recuperação de senha
     *
     * Requisições: @GET e @POST
     * @api
     * @return JsonModel|ViewModel
     */
    public function recoverPasswordAction()
    {
        $messageError = null;
        $request = $this->getRequest();
        $urlRequest = $this->url()->fromRoute();
        $apiRequest = strpos($urlRequest, 'api');

        if($request->isPost()) {
            //recupera os dados da requisição
            $data = (array)json_decode($request->getContent());

            if(isset($data['email'])) {
                //realiza um select no DB para obter a informação de conta ativa do usuário
                $userData = $this->entityManager->createQueryBuilder()
                    ->select('u.id, u.activeAccount, profile.fullName as name')
                    ->from(User::class, 'u')
                    ->leftJoin('u.profile', 'profile')
                    ->where('u.email like :email')
                    ->setParameter('email', $data['email'])->getQuery()->getArrayResult();
                $userData = count($userData) > 0 ? $userData[0] : array();

                if(isset($userData['activeAccount']) && $userData['activeAccount']) {
                    $newPassword = $this->generatePassword();

                    try {
                        //retorna o usuário e atualiza a sua senha salva no DB
                        $user = $this->entityManager->find(User::class, $userData['id']);

                        if(!$user instanceof User) {
                            throw new Exception('Usuário não encontrado.');
                        }

                        $user->setPassword($newPassword);
                        $this->entityManager->persist($user);
                        $this->entityManager->flush();
                    } catch (Exception $exception) {
                        //retorna erro interno para o usuário em caso de exceção gerada durante o processo de alteração de senha
                        $this->getResponse()->setStatusCode(500);

                        return new JsonModel(
                            array(
                                'result' => "Ocorreu um erro interno ao alterar a senha, tente novamente mais tarde.",
                                'exception' => $exception->getMessage()
                            )
                        );
                    }

                    $message = new Message();
                    $message->addTo($data['email']);
                    $message->addFrom(AuthController::EMAIL_SENDER);
                    //todo colocar o nome definido para a ferramenta
                    $message->setSubject('Recuperação de Senha do XXX');
                    $content = "Olá ".$userData['name'] .",\nutilize a senha a seguir para realizar login no XXX:\n".$newPassword;
                    $message->setBody($content);
                    $message->setEncoding('UTF-8');
                    $message->getHeaders()->addHeaderLine('Content-Type', 'text/plain; charset=UTF-8');

                    $transport = new SmtpTransport();
                    $options   = new SmtpOptions([
                        'host'              => 'smtp.gmail.com',
                        'connection_class'  => 'login',
                        'connection_config' => [
                            'ssl'       => 'tls',
                            'username' => AuthController::EMAIL_SENDER,
                            'password' => AuthController::PASS_EMAIL_SENDER,
                        ],
                    ]);
                    $options->setPort(587);
                    $transport->setOptions($options);
                    $transport->send($message);

                    return new JsonModel(array('result' => "Uma mensagem com uma nova senha de acesso foi enviada para seu e-mail."));
                } else {
                    $this->getResponse()->setStatusCode(400);
                    return new JsonModel(array('result' => "Não foi encontrada nenhuma conta cadastrada para este e-mail."));
                }
            }
        }

        $this->layout()->setTemplate('layout/auth');
        return new ViewModel();
    }

    /**
     * Realiza o login do usuário
     *
     * Requisição POST: realiza o login
     * Requisição GET: carrega o HTML com o formulário de login
     *
     * Requisições: @GET e @POST
     * @api
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
            return ($apiRequest)? (new JsonModel(array('result' => 'Usuário já logado'))) : ($this->redirect()->toRoute('tcc-home'));
        }

        //verificar o login do usuário
        if($request->isPost()) {
            //recupera os dados da requisição
            if($apiRequest) {
                $data = (array)json_decode($request->getContent());
            } else {
                $data = $this->params()->fromPost();
            }

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
                    return ($apiRequest) ? (new JsonModel(array('result' => 'Login realizado com sucesso.'))) : ($this->redirect()->toRoute('tcc-home'));
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

        $result = !empty($messageError)? array('result' => $messageError) : array();

        //verifica se a requisição foi realizada como API e retorna um JSON como resposta
        if($apiRequest) {
            return new JsonModel($result);
        }

        $this->layout()->setTemplate('layout/auth');
        return new ViewModel($result);
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

        return ($apiRequest)? new JsonModel(array('result' => "Logout realizado com sucesso.")) : $this->redirect()->toRoute('login');
    }
}