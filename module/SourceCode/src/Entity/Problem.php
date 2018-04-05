<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Entity;

use Application\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Problem
 *  Representa os enunciados de problemas
 *
 * @ORM\Entity
 * @ORM\Table(name="problems")
 * @package SourceCode\Entity
 */
class Problem extends Entity
{
    /**
     * Id de identificação do Problema
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * Título do problema
     *
     * @ORM\Column(type="string", nullable=false)
     *
     * @var string
     */
    private $title;


    /**
     * Descrição do problema
     *
     * @ORM\Column(type="text", nullable=false)
     *
     * @var string
     */
    private $description;

    /**
     * A categoria a qual o problema pertence
     *
     * @ORM\ManyToOne(targetEntity="CategoryProblem", fetch="LAZY")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * @var CategoryProblem
     */
    private $category;

    /**
     * Retorna o id de identificação do Problema
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Retorna o Título do Problema
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Define o Título do Problema
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Retorna a descrição do Problema
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Define a descrição do Problema
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Retorna a Categoria do Problema
     *
     * @return CategoryProblem
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Define a Categoria do Problema
     *
     * @param CategoryProblem $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * Retorna todos os dados do Problema em formato de array
     * @inheritdoc
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category->toArray()
        );
    }
}