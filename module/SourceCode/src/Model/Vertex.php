<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Model;

/**
 * Classe que representa o vértice de comando de desvio
 * Class Vertex
 * @package SourceCode\Model
 */
class Vertex
{
    /**
     * Nome do vértice
     *
     * @var string
     */
    private $name;

    /**
     * Índice do vértice que se liga a direita
     *
     * @var int
     */
    private $rightVertexIndex;

    /**
     * Índice do vértice que se liga a esquerda
     *
     * @var int
     */
    private $leftVertexIndex;

    /**
     * Armazena os índices dos vértices que tem ligação com o vértice
     *
     * @var array
     */
    private $moreVertexIndexes;

    /**
     * Coordenada x do vértice na tela
     *
     * @var int
     */
    private $x;

    /**
     * Coordenada y do vértice na tela
     *
     * @var int
     */
    private $y;

    /**
     * Índice do vértice de abertura do bloco
     *
     * @var int
     */
    private $openingVertexIndex;

    /**
     * Linha inicial do comando no código fonte
     *
     * @var int
     */
    private $initialLineNumber;

    /**
     * Linha final do comando no código fonte
     *
     * @var int
     */
    private $endLineNumber;

    /**
     * Retorna o nome do vértice
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Define o nome do vértice
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Retorna o índice do vértice que se liga a direita
     *
     * @return int
     */
    public function getRightVertexIndex()
    {
        return $this->rightVertexIndex;
    }

    /**
     * Define o índice do vértice que se liga a direita
     *
     * @param int $rightVertexIndex
     */
    public function setRightVertexIndex($rightVertexIndex)
    {
        $this->rightVertexIndex = $rightVertexIndex;
    }

    /**
     * Retorna o índice do vértice que se liga a esquerda
     *
     * @return int
     */
    public function getLeftVertexIndex()
    {
        return $this->leftVertexIndex;
    }

    /**
     * Define o índice do vértice que se liga a esquerda
     *
     * @param int $leftVertexIndex
     */
    public function setLeftVertexIndex($leftVertexIndex)
    {
        $this->leftVertexIndex = $leftVertexIndex;
    }

    /**
     * @return array
     */
    public function getMoreVertexIndexes()
    {
        return $this->moreVertexIndexes;
    }

    /**
     * @param array $moreVertexIndexes
     */
    public function setMoreVertexIndexes($moreVertexIndexes)
    {
        $this->moreVertexIndexes = $moreVertexIndexes;
    }

    /**
     * Retorna a coordenada x do vértice na tela
     *
     * @return int
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * Define a coordenada x do vértice na tela
     *
     * @param int $x
     */
    public function setX($x)
    {
        $this->x = $x;
    }

    /**
     * Retorna a coordenada y do vértice na tela
     *
     * @return int
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * Define a coordenada y do vértice na tela
     *
     * @param int $y
     */
    public function setY($y)
    {
        $this->y = $y;
    }

    /**
     * Retorna o índice do vértice de abertura do bloco
     *
     * @return int
     */
    public function getOpeningVertexIndex()
    {
        return $this->openingVertexIndex;
    }

    /**
     * Define o índice do vértice de abertura do bloco
     *
     * @param int $openingVertexIndex
     */
    public function setOpeningVertexIndex($openingVertexIndex)
    {
        $this->openingVertexIndex = $openingVertexIndex;
    }

    /**
     * Retorna a linha inicial do comando no código fonte
     *
     * @return int
     */
    public function getInitialLineNumber()
    {
        return $this->initialLineNumber;
    }

    /**
     * Define a linha inicial do comando no código fonte
     *
     * @param int $initialLineNumber
     */
    public function setInitialLineNumber($initialLineNumber)
    {
        $this->initialLineNumber = $initialLineNumber;
    }

    /**
     * Retorna a linha final do comando no código fonte
     *
     * @return int
     */
    public function getEndLineNumber()
    {
        return $this->endLineNumber;
    }

    /**
     * Define a linha final do comando no código fonte
     *
     * @param int $endLineNumber
     */
    public function setEndLineNumber($endLineNumber)
    {
        $this->endLineNumber = $endLineNumber;
    }

    public function toArray() {
        return array(
            'name'                 => $this->name,
            'rightVertexIndex'     => $this->rightVertexIndex,
            'leftVertexIndex'      => $this->leftVertexIndex,
            'moreVertexIndexes' => $this->moreVertexIndexes,
            'x'                    => $this->x,
            'y'                    => $this->y,
            'openingVertexIndex'   => $this->openingVertexIndex,
            'initialLineNumber'    => $this->initialLineNumber,
            'endLineNumber'        => $this->endLineNumber,
        );
    }
}