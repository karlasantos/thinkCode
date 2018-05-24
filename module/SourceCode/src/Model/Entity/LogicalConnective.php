<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Entity;

use Application\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class LogicalConnective
 * Representa o Operador Lógico de uma determinada Linguagem de Programação
 *
 * @ORM\Entity
 * @ORM\Table(name="logical_connectives")
 * @package SourceCode\Entity
 */
class LogicalConnective extends Entity
{
    /**
     * Id de identificação do Operador Lógico
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * Nome do Operador Lógico
     *
     * @ORM\Column(type="string", unique=true, nullable=false)
     *
     * @var string
     */
    private $name;

    /**
     * Uma coleção de todas as Linguagens de Programação que esse operador está inserido
     *
     * @ORM\ManyToMany(targetEntity="Language", mappedBy="logicalConnectives")
     *
     * @var ArrayCollection
     */
    private $languages;

    /**
     * Retorna o Id de identificação do Operador Lógico
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Retorna o nome do Operador Lógico
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Define o nome do Operador Lógico
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Retorna uma coleção de todas as Linguagens de Programação que esse operador está inserido
     *
     * @return ArrayCollection
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Define uma coleção de todas as Linguagens de Programação que esse operador está inserido
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
            'name' => $this->name
        );
    }
}