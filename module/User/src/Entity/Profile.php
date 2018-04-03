<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace User\Entity;

use Application\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Profile
 *
 * @ORM\Entity
 * @ORM\Table(name="profiles")
 * @package User\Entity
 */
class Profile extends Entity
{
    /**
     * Id de identificação do perfil
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * Nome do perfil de usuário
     *
     * @ORM\Column(name="full_name", type="string", nullable=false)
     *
     * @var string
     */
    private $fullName;

    /**
     * Imagem de avatar do perfil
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    private $avatar;

    /**
     * Data de aniversário
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    private $birthday;

    /**
     * Curso de graduação
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    private $school;

    /**
     * Gênero
     * @example "Feminino" ou "Masculino"
     *
     * @ORM\Column(type="string", nullable=false, length=10)
     *
     * @var string
     */
    private $gender;

    /**
     * Usuário dono do perfil
     *
     * @ORM\OneToOne(targetEntity="User", mappedBy="profile", cascade={"persist", "remove"})
     * @var User
     */
    private $user;

    /**
     * Retorna o id de identificação do perfil
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Retorna o nome do perfil
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Define o nome do perfil
     *
     * @param string $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * Retorna o caminho do avatar do perfil
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Define o caminho do avatar do perfil
     *
     * @param string $avatar
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * Retorna a data de aniversário do usuário
     *
     * @return string
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Define a data de aniversário do usuário
     *
     * @param string $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * Retorna o curso do usuário
     *
     * @return string
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * Define o curso do usuário
     *
     * @param string $school
     */
    public function setSchool($school)
    {
        $this->school = $school;
    }

    /**
     * Retorna o gênero do usuário
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Define o gênero do usuário
     *
     * @param string $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * Retorna o usuário dono do perfil
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Define o usuário dono do perfil
     *
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Retorna os todos dados do Perfil de Usuário em formato de array
     * @inheritdoc
     * @return array
     */
    public function toArray()
    {
        return array(
            'id'       => $this->id,
            'fullName' => $this->fullName,
            'avatar'   => $this->avatar,
            'school'   => $this->school,
            'birthday' => $this->birthday,
            'gender'   => $this->gender
        );
    }
}