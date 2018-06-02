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

/**
 * Class DataType
 * Representa o tipo de dado de uma determinada Linguagem de Programação
 *
 * @ORM\Entity Mapeamento Objeto Relacional
 * @ORM\Table(name="data_types")
 * @package SourceCode\Model\Entity
 */
class DataType extends Entity
{
    /**
     * Id de identificação do Tipo de Dado
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * Nome do Tipo de Dado
     *
     * @ORM\Column(type="string", unique=true, nullable=false)
     *
     * @var string
     */
    private $name;

    /**
     * Tamanho do Tipo de Dado em bytes
     *
     * @ORM\Column(name="byte_size", type="decimal", nullable=false)
     *
     * @var float
     */
    private $byteSize;

    /**
     * Uma coleção de todas as Linguagens de Programação que esse comando está inserido
     *
     * @ORM\ManyToMany(targetEntity="Language", mappedBy="dataTypes")
     *
     * @var ArrayCollection
     */
    private $languages;

    /**
     * Retorna o id de identificação do tipo de dado
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Retorna o nome do tipo de dado
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Define o nome do tipo de dado
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Retorna o tamanho em bytes do tipo de dado
     *
     * @return float
     */
    public function getByteSize()
    {
        return $this->byteSize;
    }

    /**
     * Define o tamanho em bytes do tipo de dado
     *
     * @param float $byteSize
     */
    public function setByteSize($byteSize)
    {
        $this->byteSize = $byteSize;
    }

    /**
     * Retorna uma coleção de todas as Linguagens de Programação que esse tipo de dado está inserido
     *
     * @return ArrayCollection
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Define uma coleção de todas as Linguagens de Programação que esse tipo de dado está inserido
     *
     * @param ArrayCollection $languages
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;
    }

    /**
     * Retorna todos os dados do DataType em formato de array
     * @inheritdoc
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'byteSize' => $this->byteSize,
        );
    }
}