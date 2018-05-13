<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace User\Controller;

use Application\Controller\RestfulController;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use SourceCode\Entity\Language;
use User\Entity\Profile;
use User\Entity\User;
use User\Validation\ProfileValidator;
use User\Validation\UserValidator;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use Exception;

/**
 * Class UserController
 * Controller de Usuário responsável por tratar as requisições de criação, atualização,
 * deleção de usuários e carregamento de views de usuários.
 * @package User\Controller
 */
class UserController extends RestfulController
{
    const INTERNAL_ERROR_SAVE = 'Ocorreu um erro interno e não foi possível salvar o usuário.';
    const USER_NOT_FOUND = 'Usuário não encontrado';

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager);
    }

    public function indexAction()
    {
        return new ViewModel();
    }

    public function settingsAction()
    {
        return new ViewModel(array('id' => $_SESSION['Zend_Auth']->getArrayCopy()['storage']['id']));
    }

    public function getList()
    {
        return new JsonModel([
            'results' => array(),
        ]);
    }

    /**
     * Retorna todos os dados de um usuário através de seu id
     *
     * @param int $id
     * @return JsonModel
     */
    public function get($id)
    {
        $id = intval($id);
        //realiza um select no DB para obter as informação de conta do usuário
        $user = $this->entityManager->createQueryBuilder()
            ->select('partial u.{id, email, activeAccount, created}, partial defaultLanguage.{id, name}')
            ->addSelect('partial profile.{id, fullName, avatar, birthday, school, gender}')
            ->from(User::class, 'u')
            ->leftJoin('u.profile', 'profile')
            ->leftJoin('profile.defaultLanguage', 'defaultLanguage')
            ->where('u = :userId')
            ->setParameter('userId', $id)
            ->getQuery()->getArrayResult();
        if(count($user) > 0) {
            $user = $user[0];
            $user['created'] =  $user['created']->format('d/m/Y H:i:s');
            $user['profile']['defaultLanguageId'] =  $user['profile']['defaultLanguage']['id'];
        } else {
            $this->getResponse()->setStatusCode(400);
            $user = array();
        }

        return new JsonModel([
            'result' => $user,
        ]);
    }

    /**
     * Cria um novo usuário
     *
     * @api
     * @param array $data dados para novo usuário
     * @return JsonModel
     */
    public function create($data)
    {
        //instancia validadores dos dados
        $userFilter = new UserValidator($this->entityManager, $data, true);
        $profileFilter = new ProfileValidator($data);

        //se houver erro nos dados enviados na requisição retorna mensagem de erro
        if(!$userFilter->isValid() || !$profileFilter->isValid()) {
            $this->getResponse()->setStatusCode(400);

            $messages = array();
            //monta as mensagens de erro do usuário
            foreach ($userFilter->getMessages() as $message) {
                if(count($message) > 0) {
                    $messages[] = array_shift($message);
                }
            }

            //monta as mensagens de erro do perfil
            foreach ($profileFilter->getMessages() as $message) {
                if(count($message) > 0) {
                    $messages[] = array_shift($message);
                }
            }

            return new JsonModel(
                array(
//                    'result' => array_merge( $userFilter->getMessages(), $profileFilter->getMessages())
                    'result' => $messages
                )
            );
        }

        //instancia o novo usuário com seu perfil se dados estiverem corretos
        $user = new User();
        $user->setData($userFilter->getValues());

        $profile = new Profile();
        $profile->setData($profileFilter->getValues());

        $user->setProfile($profile);
        $profile->setUser($user);

        try {
            //persiste os dados no DB
            $this->entityManager->persist($profile);
            $this->entityManager->persist($user);

            $this->entityManager->flush();
        } catch (Exception $exception) {
            //retorna erro interno para o usuário em caso de exceção gerada durante o processo de persistência de dados
            $this->getResponse()->setStatusCode(500);

            return new JsonModel(
                array(
                    'result' => UserController::INTERNAL_ERROR_SAVE,
                    'exception' => $exception->getMessage()
                )
            );
        }

        return new JsonModel([
            'result' => 'Usuário criado com sucesso.'
        ]);
    }

    /**
     * Atualiza um usuário já cadastrado
     *
     * @param int $id id de identificação do usuário
     * @param array $data dados atualizados para usuário
     * @return JsonModel
     */
    public function update($id, $data)
    {
        try {
            //procura o usuário para edição através do id informado na requisição
            $user = $this->entityManager->find(User::class, $id);

            //se usuário não for encontrado retorna mensagem erro de NOT_FOUND
            if(!$user instanceof User) {
                $this->getResponse()->setStatusCode(400);

                return new JsonModel(
                    array(
                        'result' => UserController::USER_NOT_FOUND,
                    )
                );
            }

            //todo colocar verificação se está sendo acessado o mesmo usuário logado

            //recupera o perfil do usuário
            $profile = $user->getProfile();

            //verifica se o usuário selecionou a opção de alteração de senha e prepara os dados
            if(isset($data['changePassword']) && $data['changePassword']) {
                if(!password_verify($data['oldPassword'], $user->getPassword())) {
                    $this->getResponse()->setStatusCode(400);

                    return new JsonModel(
                        array(
                            'result' => 'Senha atual está incorreta.'
                        )
                    );
                }

                $data['password'] = $data['newPassword'];
            }

            //instancia validadores dos dados
            $userFilter = new UserValidator($this->entityManager, $data);
            $profileFilter = new ProfileValidator($data['profile']);

            //se houver erro nos dados enviados na requisição retorna mensagens de erro de cada campo específico
            if(!$userFilter->isValid() || !$profileFilter->isValid()) {
                $this->getResponse()->setStatusCode(400);

                $messages = array();
                //monta as mensagens de erro do usuário
                foreach ($userFilter->getMessages() as $message) {
                    if(count($message) > 0) {
                        $messages[] = array_shift($message);
                    }
                }

                //monta as mensagens de erro do perfil
                foreach ($profileFilter->getMessages() as $message) {
                    if(count($message) > 0) {
                        $messages[] = array_shift($message);
                    }
                }

                return new JsonModel(
                    array(
                        'result' => $messages
                    )
                );
            }

            //define os dados do usuário
            $user->setEmail($userFilter->getValue('email'));
            if(isset($data['changePassword']) && $data['changePassword']) {
                $user->setPassword($userFilter->getValue('password'));
            }

            //define os dados do perfil
            $profile->setData($profileFilter->getValues());

            //define a linguagem de preferência no envio de soluções
            if(isset($data['profile']['defaultLanguageId'])) {
                $language = $this->entityManager->find(Language::class, $data['profile']['defaultLanguageId']);
                if($language instanceof Language)
                    $profile->setDefaultLanguage($language);
            }

            //persiste os dados no DB
            $this->entityManager->persist($profile);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (Exception $exception) {
            //retorna erro interno para o usuário em caso de exceção gerada durante o processo de persistência de dados
            $this->getResponse()->setStatusCode(500);

            return new JsonModel(
                array(
                    'result' => UserController::INTERNAL_ERROR_SAVE,
                    'exception' => $exception->getMessage()
                )
            );
        }

        return new JsonModel([
            'result' => 'Usuário atualizado com sucesso!'
        ]);
    }

    public function delete($id)
    {
        try {
            //procura o usuário para exclusão através do id informado na requisição
            $user = $this->entityManager->find(User::class, $id);

            //se usuário não for encontrado retorna mensagem erro de NOT_FOUND
            if(!$user instanceof User) {
                $this->getResponse()->setStatusCode(400);

                return new JsonModel(
                    array(
                        'result' => UserController::USER_NOT_FOUND,
                    )
                );
            }

            //desabilita a conta do usuário
            $user->setActiveAccount(false);

            //persiste a remoção no DB
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch(Exception $exception) {
            //retorna erro interno para o usuário em caso de exceção gerada durante o processo de persistência de dados
            $this->getResponse()->setStatusCode(500);

            return new JsonModel(
                array(
                    'result' => 'Ocorreu um erro interno e não foi possível remover essa conta.',
                    'exception' => $exception->getMessage()
                )
            );
        }
        return new JsonModel([
            'result' => 'Conta desativada com sucesso!'
        ]);
    }
}