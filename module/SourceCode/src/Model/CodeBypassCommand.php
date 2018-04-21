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
     * Define a posição do comando na lista de vértices
     *
     * @param int $indexReferentNode
     */
    public function setIndexReferentNode($indexReferentNode)
    {
        $this->indexReferentNode = $indexReferentNode;
    }

    /**
     * Retorna a posição do comando responsável pela abertura de bloco: "{"
     *
     * @return int
     */
    public function getOpeningIndex()
    {
        return $this->openingIndex;
    }

    /**
     * Define a posição do comando responsável pela abertura de bloco: "{"
     *
     * @param int $openingIndex
     */
    public function setOpeningIndex($openingIndex)
    {
        $this->openingIndex = $openingIndex;
    }

    /**
     * Retorna o número da linha de início de comando
     * @return int
     */
    public function getInitialLineNumber()
    {
        return $this->initialLineNumber;
    }

    /**
     * Define o número da linha de início de comando
     *
     * @param int $initialLineNumber
     */
    public function setInitialLineNumber($initialLineNumber)
    {
        $this->initialLineNumber = $initialLineNumber;
    }

    /**
     * Retorna o número da linha de final de comando
     * @return mixed
     */
    public function getEndLineNumber()
    {
        return $this->endLineNumber;
    }

    /**
     * Define o número da linha de final de comando
     *
     * @param mixed $endLineNumber
     */
    public function setEndLineNumber($endLineNumber)
    {
        $this->endLineNumber = $endLineNumber;
    }
}