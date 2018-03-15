<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace User\Entity;

use DateTime;

/**
 * Class User
 * @package Application\Entity
 */
class User
{
    /**
     * Id de identificação do usuário
     *
     * @var int
     */
    private $id;

    /**
     * E-mail do usuário
     *
     * @var string
     */
    private $email;

    /**
     * Senha de login
     *
     * @var string
     */
    private $password;

    /**
     * @var Profile
     */
    private $profile;

    /**
     * Data de criação do usuário
     *
     * @var DateTime
     */
    private $created;

}