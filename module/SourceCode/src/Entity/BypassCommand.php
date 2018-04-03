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
 * Class BypassCommand
 * Representa o Comando de Desvio de uma determinada Linguagem de Programação
 *
 * @ORM\Entity
 * @ORM\Table(name="diversion_commands")
 * @package SourceCode\Entity
 */
class BypassCommand extends Entity
{
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
     * @ORM\Column(type="string", unique=true, nullable=false)
     *
     * @var string
     */
    private $name;

    /**
     * Tipo do Comando de Desvio (Condicional/Repetição)
     *
     * @ORM\Column(type="string", unique=true, nullable=false)
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
     * Retorna o Id de identificação do Comando de Desvio
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Retorna o nome do comando de desvio
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Define o nome do comando de desvio
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * Retorna todos os dados do Comando de Desvio em formato de array
     * @inheritdoc
     * @return array
     */
    public function toArray()
    {
        return array(
            'id'   => $this->id,
            'name' => $this->name,
            'type' => $this->type
        );
    }
}