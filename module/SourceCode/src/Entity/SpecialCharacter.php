<?php
/**
 * Created by PhpStorm.
 * User: karla
 * Date: 16/04/18
 * Time: 15:47
 */

namespace SourceCode\Entity;

use Application\Entity\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use User\Entity\User;


/**
 * Class SpecialCharacter
 * Representa um elemento do grafo de controle de fluxo
 *
 * @ORM\Entity
 * @ORM\Table(name="special_characters")
 * @package SourceCode\Entity
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
     * Método abstrato que retorna os dados do objeto em formato de array
     * @return mixed
     */
    public function toArray()
    {
        return array();
    }
}