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
 * Class BypassCommand
 * Representa o Comando de Desvio de uma determinada Linguagem de Programação
 *
 * @ORM\Entity
 * @ORM\Table(name="diversion_commands")
 * @package SourceCode\Model\Entity
 */
class BypassCommand extends Entity
{
    const TYPE_CONDITIONAL = 'conditional';
    const TYPE_LOOP = 'loop';

    //todo rever o nome desta classe
    /**
     * Id de identificação do Comando de Desvio
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * Nome do Comando de Desvio
     *
     * @ORM\Column(name="initial_command_name",type="string", nullable=false)
     *
     * @var string
     */
    private $initialCommandName;

    /**
     * Final do comando de desvio
     *
     * @ORM\Column(name="terminal_command_name",type="string", nullable=true)
     * @var
     */
    private $terminalCommandName;

    /**
     * Tipo do Comando de Desvio (Condicional/Repetição)
     *
     * @ORM\Column(type="string", nullable=false)
     *
     * @var string
     */
    private $type;

    /**
     * Uma coleção de todas as Linguagens de Programação que esse comando está inserido
     *
     * @ORM\ManyToMany(targetEntity="Language", mappedBy="diversionCommands")
     *
     * @var ArrayCollection
     */
    private $languages;

    /**
     * O elemento do grafo que representa esse comando do desvio
     *
     * @ORM\ManyToOne(targetEntity="GraphElement", fetch="LAZY")
     * @ORM\JoinColumn(name="graph_element_id", referencedColumnName="id")
     *
     * @var GraphElement
     */
    private $graphElement;

    /**
     * Retorna o Id de identificação do Comando de Desvio
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Retorna o nome do inicial de comando de desvio
     *
     * @return string
     */
    public function getInitialCommandName()
    {
        return $this->initialCommandName;
    }

    /**
     * Define o nome do inicial de comando de desvio
     *
     * @param string $initialCommandName
     */
    public function setInitialCommandName($initialCommandName)
    {
        $this->initialCommandName = $initialCommandName;
    }

    /**
     * Retorna o nome do terminal do comando de desvio
     *
     * @return mixed
     */
    public function getTerminalCommandName()
    {
        return $this->terminalCommandName;
    }

    /**
     * Define o nome do terminal do comando de desvio
     *
     * @param mixed $terminalCommandName
     */
    public function setTerminalCommandName($terminalCommandName)
    {
        $this->terminalCommandName = $terminalCommandName;
    }

    /**
     * Retorna o tipo do Comando de Desvio
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Define o tipo do Comando de Desvio
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Retorna uma coleção de todas as Linguagens de Programação que esse comando está inserido
     *
     * @return ArrayCollection
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Define uma coleção de todas as Linguagens de Programação que esse comando está inserido
     *
     * @param ArrayCollection $languages
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;
    }

    /**
     * Retorna o elemento gráfico desse comando de desvio
     *
     * @return GraphElement
     */
    public function getGraphElement()
    {
        return $this->graphElement;
    }

    /**
     * Define o elemento gráfico desse comando de desvio
     *
     * @param GraphElement $graphElement
     */
    public function setGraphElement($graphElement)
    {
        $this->graphElement = $graphElement;
    }

    /**
     * Retorna todos os dados do Comando de Desvio em formato de array
     * @inheritdoc
     * @return array
     */
    public function toArray()
    {
        return array(
            'id'   => $this->id,
            'name' => $this->initialCommandName,
            'type' => $this->type
        );
    }
}