<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Controller;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
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

class UserController extends AbstractRestfulController
{
    protected $entityManager;

    const INTERNAL_ERROR_SAVE = 'Ocorreu um erro interno e não foi possível salvar o usuário.';
    const USER_NOT_FOUND = 'Usuário não encontrado';

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function indexAction()
    {
//        return new JsonModel([
//            'results' => array(),
//        ]);
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
            return new JsonModel(
                array(
                    'result' => array_merge( $userFilter->getMessages(), $profileFilter->getMessages())
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

            //recupera o perfil do usuário
            $profile = $user->getProfile();

            //todo verificar este validator para update talvez não sirva
            //instancia validadores dos dados
            $userFilter = new UserValidator($this->entityManager, $data);
            $profileFilter = new ProfileValidator($data);

            //se houver erro nos dados enviados na requisição retorna mensagens de erro de cada campo específico
            if(!$userFilter->isValid() || !$profileFilter->isValid()) {
                $this->getResponse()->setStatusCode(400);
                return new JsonModel(
                    array(
                        'result' => array_merge( $userFilter->getMessages(), $profileFilter->getMessages())
                    )
                );
            }

            //define os dados do usuário
            $user->setEmail($userFilter->getValue('email'));

            //todo verificar essa atualização de senha
            if(!empty($userFilter->getValue('password'))) {
                $user->setPassword($userFilter->getValue('password'));
            }

            //define os dados do perfil
            $profile->setData($profileFilter->getValues());

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