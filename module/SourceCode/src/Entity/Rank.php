<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Entity;


use Application\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use User\Entity\User;

/**
 * Class Rank
 * Representa o Rank de cada enunciado de problema
 *
 * @ORM\Entity
 * @ORM\Table(name="rank")
 * @package SourceCode\Entity
 */
class Rank extends Entity
{
    /**
     * Id de identificação da Análise
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * O problema que este Rank pertence
     *
     * @ORM\ManyToOne(targetEntity="Problem", fetch="LAZY", inversedBy="rank")
     * @ORM\JoinColumn(name="problem_id", referencedColumnName="id")
     * @var Problem
     */
    private $problem;

    /**
     * O usuário que está neste Rank
     *
     * @ORM\ManyToOne(targetEntity="User\Entity\User", fetch="LAZY", inversedBy="rankings")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @var User
     */
    private $user;

    /**
     * Colocação do usuário no rank da questão
     *
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $ranking;

    /**
     * Retorna o id de identificação do Rank
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Retorna o Problema que o Rank pertence
     *
     * @return Problem
     */
    public function getProblem()
    {
        return $this->problem;
    }

    /**
     * Define o Problema que o Rank pertence
     *
     * @param Problem $problem
     */
    public function setProblem($problem)
    {
        $this->problem = $problem;
    }

    /**
     * Retorna o Usuário que o Rank pertence
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Define o Usuário que o Rank pertence
     *
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Retorna a posição do Usuário no Rank daquela questão
     *
     * @return int
     */
    public function getRanking()
    {
        return $this->ranking;
    }

    /**
     * Define a posição do Usuário no Rank daquela questão
     *
     * @param int $ranking
     */
    public function setRanking($ranking)
    {
        $this->ranking = $ranking;
    }

    /**
     * Método que retorna os dados do Rank em formato de array
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->id,
            'problemId' => $this->problem->getId(),
            'userId' => $this->user->getId(),
            'ranking' => $this->ranking,
        );
    }
}