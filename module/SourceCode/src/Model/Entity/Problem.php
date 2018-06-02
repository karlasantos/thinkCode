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
 * Class Problem
 *  Representa os enunciados de problemas
 *
 * @ORM\Entity Mapeamento Objeto Relacional
 * @ORM\Table(name="problems")
 * @package SourceCode\Model\Entity
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

    //@todo rever se continuar usando esse array collection
    /**
     * Uma coleção de todos os códigos fonte submetidos para esse problema
     *
     * @ORM\OneToMany(targetEntity="SourceCode", mappedBy="problem")
     * @ORM\JoinColumn(name="id", referencedColumnName="problem_id")
     *
     * @var ArrayCollection
     */
    private $sourceCodes;

    /**
     * Uma coleção o Rank de usuários que resolveram esse problema
     *
     * @ORM\OneToMany(targetEntity="Rank", mappedBy="problem")
     * @ORM\JoinColumn(name="id", referencedColumnName="problem_id")
     * @ORM\OrderBy({"ranking" = "ASC"})
     *
     * @var ArrayCollection
     */
    private $rank;

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
     * Retorna uma coleção com todos os códigos fonte submetidos para esse problema
     *
     * @return ArrayCollection
     */
    public function getSourceCodes()
    {
        return $this->sourceCodes;
    }

    /**
     * Define uma coleção com todos os códigos fonte submetidos para esse problema
     *
     * @param ArrayCollection $sourceCodes
     */
    public function setSourceCodes($sourceCodes)
    {
        $this->sourceCodes = $sourceCodes;
    }

    /**
     * Retorna o Rank de usuários que resolveram esse problema
     *
     * @return ArrayCollection
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Define o Rank de usuários que resolveram esse problema
     *
     * @param ArrayCollection $rank
     */
    public function setRank($rank)
    {
        $this->rank = $rank;
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