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
 * Class GraphElement
 * Representa um elemento do grafo de controle de fluxo
 *
 * @ORM\Entity
 * @ORM\Table(name="graph_elements")
 * @package SourceCode\Model\Entity
 */
class GraphElement extends Entity
{
    /**
     * Id de identificação do Elemento do Grafo
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * Nome do elemento do Grafo
     *
     * @ORM\Column(type="string", unique=true, nullable=false)
     *
     * @var string
     */
    private $name;

    /**
     * Imagem do elemento do Grafo
     *
     * @ORM\Column(name="model_image", type="string", nullable=true)
     *
     * @var string
     */
    private $modelImage;

    /**
     * Tipo do elemento do grafo (Condicional/Repetição)
     *
     * @ORM\Column(type="string", nullable=false)
     *
     * @var string
     */
    private $type;

    /**
     * Retorna o id de identificação do elemento do grafo
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Retorna o nome do elemento do grafo
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Define o nome do elemento do grafo
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Retorna a imagem de representação do elemento do grafo
     *
     * @return string
     */
    public function getModelImage()
    {
        return $this->modelImage;
    }

    /**
     * Define a imagem de representação do elemento do grafo
     *
     * @param string $modelImage
     */
    public function setModelImage($modelImage)
    {
        $this->modelImage = $modelImage;
    }

    /**
     * Retorna o tipo do elemento do grafo
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Retorna o tipo do elemento do grafo
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
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