<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * Class User
 *
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @package User\Entity
 */
class User
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
     * @ORM\Column(type="string", nullable=false)
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
     * @param DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }
}