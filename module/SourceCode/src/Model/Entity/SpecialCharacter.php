<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Model\Entity;

use Application\Model\Entity\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use User\Model\Entity\User;


/**
 * Class SpecialCharacter
 * Representa um elemento do grafo de controle de fluxo
 *
 * @ORM\Entity Mapeamento Objeto Relacional
 * @ORM\Table(name="special_characters")
 * @package SourceCode\Model\Entity
 */
class SpecialCharacter extends Entity
{
    /**
     * Id de identificação do Caractere Especial
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;


    /**
     * Nome do Caractere Especial
     *
     * @ORM\Column(type="string", unique=true, nullable=false)
     *
     * @var string
     */
    private $name;

    /**
     * Uma coleção de todas as Linguagens de Programação que esse comando está inserido
     *
     * @ORM\ManyToMany(targetEntity="Language", mappedBy="specialCharacters")
     *
     * @var ArrayCollection
     */
    private $languages;

    /**
     * Retorna o id de identificação do Caractere Especial
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Retorna o nome do Caractere Especial
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Define o nome do Caractere Especial
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Retorna uma coleção de todas as Linguagens de Programação que esse caractere está inserido
     *
     * @return ArrayCollection
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Define uma coleção de todas as Linguagens de Programação que esse caractere está inserido
     *
     * @param ArrayCollection $languages
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;
    }

    /**
     * Método que retorna os dados do SpecialCharacter em formato de array
     * @inheritdoc
     * @return array
     */
    public function toArray()
    {
        return array();
    }
}