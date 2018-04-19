<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */
namespace SourceCode\Model;

/**
 * Classe que armazena o comando de desvio pertencente ao código fonte
 * Class CodeBypassCommand
 * @package SourceCode\Model
 */
class CodeBypassCommand
{
    /**
     * Nome do comando de desvio
     *
     * @var string
     */
    private $name;

    /**
     * Posição do comando na lista de vértices
     *
     * @var int
     */
    private $indexReferentNode;

    /**
     * Posição do comando responsável pela abertura de bloco: "{"
     * @var int
     */
    private $openingIndex;

    /**
     * Número da linha de início de comando
     *
     * @var int
     */
    private $initialLineNumber;

    /**
     * Número da linha de final de comando
     *
     * @var
     */
    private $endLineNumber;

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
     * Retorna a posição do comando na lista de vértices
     *
     * @return int
     */
    public function getIndexReferentNode()
    {
        return $this->indexReferentNode;
    }

    /**
     * @param int $indexReferentNode
     */
    public function setIndexReferentNode($indexReferentNode)
    {
        $this->indexReferentNode = $indexReferentNode;
    }

    /**
     * @return int
     */
    public function getOpeningIndex()
    {
        return $this->openingIndex;
    }

    /**
     * @param int $openingIndex
     */
    public function setOpeningIndex($openingIndex)
    {
        $this->openingIndex = $openingIndex;
    }

    /**
     * @return int
     */
    public function getInitialLineNumber()
    {
        return $this->initialLineNumber;
    }

    /**
     * @param int $initialLineNumber
     */
    public function setInitialLineNumber($initialLineNumber)
    {
        $this->initialLineNumber = $initialLineNumber;
    }

    /**
     * @return mixed
     */
    public function getEndLineNumber()
    {
        return $this->endLineNumber;
    }

    /**
     * @param mixed $endLineNumber
     */
    public function setEndLineNumber($endLineNumber)
    {
        $this->endLineNumber = $endLineNumber;
    }
}