<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Model\Entity;

use Application\Model\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Language
 * Representa as Linguagens de Programação suportadas pela aplicação
 *
 * @ORM\Entity
 * @ORM\Table(name="languages")
 * @package SourceCode\Model\Entity
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
     * Comando de início de código da Linguagem
     *
     * @ORM\Column(name="initial_code_structure", type="string", nullable=false)
     *
     * @var string
     */
    private $initialCodeStructure;

    /**
     * Comando de fim de código da Linguagem
     *
     * @ORM\Column(name="end_code_structure", type="string", nullable=false)
     *
     * @var string
     */
    private $endCodeStructure;

    /**
     * Nome do vértice de início de grafo de fluxo da linguagem
     *
     * @ORM\Column(name="initial_vertex_name", type="string", nullable=false)
     *
     * @var string
     */
    private $initialVertexName;

    /**
     * Nome do vértice de final de grafo de fluxo da linguagem
     *
     * @ORM\Column(name="end_vertex_name", type="string", nullable=false)
     *
     * @var string
     */
    private $endVertexName;

    /**
     * Nome do vértice then do comando de desvio if
     *
     * @ORM\Column(name="if_then_name_vertex", type="string", nullable=false, options={"default": "then"})
     *
     * @var string
     */
    private $ifThenNameVertex;

    /**
     * Uma coleção de todos os comandos de desvio da Linguagem de Programação
     *
     * @ORM\ManyToMany(targetEntity="BypassCommand", inversedBy="languages")
     * @ORM\JoinTable(name="language__bypass_command",
     *      joinColumns={@ORM\JoinColumn(name="language_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="bypass_command_id", referencedColumnName="id")}
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
     *      joinColumns={@ORM\JoinColumn(name="language_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="logical_connective_id", referencedColumnName="id")}
     *      )
     *
     * @var ArrayCollection
     */
    private $logicalConnectives;


    /**
     * Uma coleção de todos os tipos de dados da Linguagem de Programação
     *
     * @ORM\ManyToMany(targetEntity="DataType", inversedBy="languages")
     * @ORM\JoinTable(name="language__data_type",
     *      joinColumns={@ORM\JoinColumn(name="language_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="data_type_id", referencedColumnName="id")}
     *      )
     *
     * @var ArrayCollection
     */
    private $dataTypes;

    /**
     * Uma coleção de todos os Caracteres Especiais da Linguagem de Programação
     *
     * @ORM\ManyToMany(targetEntity="SpecialCharacter", inversedBy="languages")
     * @ORM\JoinTable(name="language__special_character",
     *      joinColumns={@ORM\JoinColumn(name="language_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="special_character_id", referencedColumnName="id")}
     *      )
     *
     * @var ArrayCollection
     */
    private $specialCharacters;

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
     * Retorna o comando de início de código da Linguagem
     *
     * @return string
     */
    public function getInitialCodeStructure()
    {
        return $this->initialCodeStructure;
    }

    /**
     * Define o comando de início de código da Linguagem
     *
     * @param string $initialCodeStructure
     */
    public function setInitialCodeStructure($initialCodeStructure)
    {
        $this->initialCodeStructure = $initialCodeStructure;
    }

    /**
     * Retorna o comando de final de código da Linguagem
     *
     * @return string
     */
    public function getEndCodeStructure()
    {
        return $this->endCodeStructure;
    }

    /**
     * Define o comando de final de código da Linguagem
     *
     * @param string $endCodeStructure
     */
    public function setEndCodeStructure($endCodeStructure)
    {
        $this->endCodeStructure = $endCodeStructure;
    }

    /**
     * Retorna o nome do vértice de início de grafo de fluxo da linguagem
     *
     * @return string
     */
    public function getInitialVertexName()
    {
        return $this->initialVertexName;
    }

    /**
     * Define o nome do vértice de início de grafo de fluxo da linguagem
     *
     * @param string $initialVertexName
     */
    public function setInitialVertexName($initialVertexName)
    {
        $this->initialVertexName = $initialVertexName;
    }

    /**
     * Retorna o nome do vértice de final de grafo de fluxo da linguagem
     *
     * @return string
     */
    public function getEndVertexName()
    {
        return $this->endVertexName;
    }

    /**
     * Define o nome do vértice de final de grafo de fluxo da linguagem
     *
     * @param string $endVertexName
     */
    public function setEndVertexName($endVertexName)
    {
        $this->endVertexName = $endVertexName;
    }

    /**
     * Retorna o nome do vértice then do comando de desvio if
     *
     * @return string
     */
    public function getIfThenNameVertex()
    {
        return $this->ifThenNameVertex;
    }

    /**
     * Define o nome do vértice then do comando de desvio if
     *
     * @param string $ifThenNameVertex
     */
    public function setIfThenNameVertex($ifThenNameVertex)
    {
        $this->ifThenNameVertex = $ifThenNameVertex;
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
     * Define uma coleção de todos os tipos de dados da Linguagem de Programação
     *
     * @return ArrayCollection
     */
    public function getDataTypes()
    {
        return $this->dataTypes;
    }

    /**
     * Retorna uma coleção de todos os tipos de dados da Linguagem de Programação
     *
     * @param ArrayCollection $dataTypes
     */
    public function setDataTypes($dataTypes)
    {
        $this->dataTypes = $dataTypes;
    }

    /**
     * @return ArrayCollection
     */
    public function getSpecialCharacters()
    {
        return $this->specialCharacters;
    }

    /**
     * Retorna uma coleção de todos os Caracteres Especiais da Linguagem de Programação
     *
     * @param ArrayCollection $specialCharacters
     */
    public function setSpecialCharacters($specialCharacters)
    {
        $this->specialCharacters = $specialCharacters;
    }

    /**
     * Define uma coleção de todos os Caracteres Especiais da Linguagem de Programação
     *
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