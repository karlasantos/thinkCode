<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Model\Entity;


use Application\Model\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class CategoryProblem
 * Representa a Categoria de cada problema
 *
 * @ORM\Entity Mapeamento Objeto Relacional
 * @ORM\Table(name="categories_problem")
 * @package SourceCode\Model\Entity
 */
class CategoryProblem extends Entity
{
    /**
     * Id de identificação da Categoria
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * Nome da Categoria
     *
     * @ORM\Column(type="string", unique=true, nullable=false)
     *
     * @var string
     */
    private $name;

    /**
     * Descrição mais detalhada da Categoria
     *
     * @ORM\Column(type="text", nullable=false)
     *
     * @var string
     */
    private $description;

    /**
     * Retorna o id de identificação da Categoria
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Retorna o nome da Categoria
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Define o nome da Categoria
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Retorna a descrição da Categoria
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Define a descrição da categoria
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Retorna todos os dados da Categoria em formato de array
     * @inheritdoc
     * @return array
     */
    public function toArray()
    {
        return array(
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description
        );
    }
}