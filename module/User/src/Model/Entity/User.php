<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace User\Model\Entity;

use Application\Model\Entity\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use SourceCode\Model\Entity\Language;

/**
 * Class User
 *
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @package User\Model\Entity
 */
class User extends Entity
{
    /**
     * Id de identificação do usuário
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * E-mail do usuário
     *
     * @ORM\Column(type="string", unique=true, nullable=false)
     *
     * @var string
     */
    private $email;

    /**
     * Senha de login
     *
     * @ORM\Column(type="string", nullable=false)
     *
     * @var string
     */
    private $password;

    /**
     * Perfil do usuário
     *
     * @ORM\OneToOne(targetEntity="Profile", inversedBy="user", fetch="LAZY")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @var Profile
     */
    private $profile;

    /**
     * Data de criação do usuário
     *
     * @ORM\Column(type="datetime", nullable=false)
     *
     * @var DateTime
     */
    private $created;

    /**
     * Indica se a conta de usuário está ativa
     *
     * @ORM\Column(name="active_account", type="boolean", nullable=false, options={"default": true})
     * @var boolean
     */
    private $activeAccount;

//    /**
//     * Uma coleção das colocações desse usuário nos Ranks
//     *
//     * @ORM\OneToMany(targetEntity="SourceCode\Model\Entity\Rank", mappedBy="user")
//     * @ORM\JoinColumn(name="id", referencedColumnName="user_id")
//     *
//     * @var ArrayCollection
//     */
//    private $rankings;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->setCreated();
        $this->setActiveAccount(true);
    }

    /**
     * Retorna o id de identificação do usuário
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Retorna o email do usuário
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Define o email do usuário
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Retorna a senha do usuário
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Define a senha do usuário
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $password = password_hash($password, PASSWORD_BCRYPT);
        $this->password = $password;
    }

    /**
     * Retorna o perfil do usuário
     *
     * @return Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Define o perfil do usuário
     *
     * @param Profile $profile
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;
    }

    /**
     * Retorna a data de criação do usuário
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Define a data de criação do usuário
     *
     * @param DateTime|null $created
     */
    public function setCreated($created = null)
    {
        if ($created === null) {
            $created = new DateTime('now');
        }

        $this->created = $created;
    }

    /**
     * Retorna se a conta de usuário está ativa
     *
     * @return bool
     */
    public function isActiveAccount()
    {
        return $this->activeAccount;
    }

    /**
     * Define se a conta de usuário está ativa
     *
     * @param bool $activeAccount
     */
    public function setActiveAccount($activeAccount)
    {
        $this->activeAccount = $activeAccount;
    }

//    /**
//     * Retorna as colocações que esse usuário está em cada questão
//     *
//     * @return ArrayCollection
//     */
//    public function getRankings()
//    {
//        return $this->rankings;
//    }
//
//    /**
//     * Define as colocações que esse usuário está em cada questão
//     *
//     * @param ArrayCollection $rankings
//     */
//    public function setRankings($rankings)
//    {
//        $this->rankings = $rankings;
//    }

    /**
     * Retorna todos os dados do Usuário em formato de array
     * @inheritdoc
     * @return array
     */
    public function toArray()
    {
        return array(
            'id'       => $this->id,
            'email'    => $this->email,
            'password' => $this->password,
            'created'  => $this->created->format('d-m-Y H:i:s'),
            'profile'  => $this->profile->toArray()
        );
    }
}