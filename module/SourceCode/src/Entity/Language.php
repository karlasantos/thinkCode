<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Entity;

use Application\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Language
 * Representa as Linguagens de Programação suportadas pela aplicação
 *
 * @ORM\Entity
 * @ORM\Table(name="languages")
 * @package SourceCode\Entity
 */
class Language extends Entity
{
    /**
     * Id de identificação da Linguagem de Programação
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * Nome da Linguagem de Programação
     *
     * @ORM\Column(type="string", unique=true, nullable=false)
     *
     * @var string
     */
    private $name;

    /**
     * Uma coleção de todos os comandos de desvio da Linguagem de Programação
     *
     * @ORM\ManyToMany(targetEntity="BypassCommand", inversedBy="languages")
     * @ORM\JoinTable(name="language__bypass_command",
     *      joinColumns={@ORM\JoinColumn(name="bypass_command_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="language_id", referencedColumnName="id")}
     *      )
     *
     * @var ArrayCollection
     */
    private $diversionCommands;

    /**
     * Uma coleção de todos os operadores lógicos da Linguagem de Programação
     *
     * @ORM\ManyToMany(targetEntity="LogicalConnective", inversedBy="languages")
     * @ORM\JoinTable(name="language__logical_connective",
     *      joinColumns={@ORM\JoinColumn(name="logical_connective_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="language_id", referencedColumnName="id")}
     *      )
     *
     * @var ArrayCollection
     */
    private $logicalConnectives;

    /**
     * Retorna o Id de identificação da Linguagem de Programação
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Retorna o nome da Linguagem de Programação
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Define o nome da Linguagem de Programação
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Retorna uma coleção de todos os comandos de desvio da Linguagem de Programação
     *
     * @return ArrayCollection
     */
    public function getDiversionCommands()
    {
        return $this->diversionCommands;
    }

    /**
     * Define uma coleção de todos os comandos de desvio da Linguagem de Programação
     *
     * @param ArrayCollection $diversionCommands
     */
    public function setDiversionCommands($diversionCommands)
    {
        $this->diversionCommands = $diversionCommands;
    }

    /**
     *  Retorna uma coleção de todos os operadores lógicos da Linguagem de Programação
     *
     * @return ArrayCollection
     */
    public function getLogicalConnectives()
    {
        return $this->logicalConnectives;
    }

    /**
     * Define uma coleção de todos os operadores lógicos da Linguagem de Programação
     *
     * @param ArrayCollection $logicalConnectives
     */
    public function setLogicalConnectives($logicalConnectives)
    {
        $this->logicalConnectives = $logicalConnectives;
    }

    /**
     * Retorna todos os dados da Linguagem de Programação em formato de array
     * @inheritdoc
     * @return array
     */
    public function toArray()
    {
        return array(
            'id'   => $this->id,
            'name' => $this->name,
        );
    }
}