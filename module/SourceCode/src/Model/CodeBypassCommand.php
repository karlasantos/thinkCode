<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */
namespace SourceCode\Model;
use SourceCode\Model\Entity\BypassCommand;

/**
 * Class CodeBypassCommand
 * Classe que armazena o comando de desvio pertencente ao código fonte
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
    private $referentVertexIndex;

    /**
     * Posição do comando responsável pela abertura de bloco: "{"
     * @var int
     */
    private $openingCommandIndex;

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
     * Comando de desvio correspondente na linguagem
     *
     * @var BypassCommand
     */
    private $bypassCommandLanguage;

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
    public function getReferentVertexIndex()
    {
        return $this->referentVertexIndex;
    }

    /**
     * Define a posição do comando na lista de vértices
     *
     * @param int $referentVertexIndex
     */
    public function setReferentVertexIndex($referentVertexIndex)
    {
        $this->referentVertexIndex = $referentVertexIndex;
    }

    /**
     * Retorna a posição do comando responsável pela abertura de bloco: "{"
     *
     * @return int
     */
    public function getOpeningCommandIndex()
    {
        return $this->openingCommandIndex;
    }

    /**
     * Define a posição do comando responsável pela abertura de bloco: "{"
     *
     * @param int $openingCommandIndex
     */
    public function setOpeningCommandIndex($openingCommandIndex)
    {
        $this->openingCommandIndex = $openingCommandIndex;
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

    /**
     * Retorna o comando de desvio correspondente na linguagem
     *
     * @return BypassCommand
     */
    public function getBypassCommandLanguage()
    {
        return $this->bypassCommandLanguage;
    }

    /**
     * Define o comando de desvio correspondente na linguagem
     *
     * @param BypassCommand $bypassCommandLanguage
     */
    public function setBypassCommandLanguage($bypassCommandLanguage)
    {
        $this->bypassCommandLanguage = $bypassCommandLanguage;
    }

    /**
     * Retorna os dados do CodeBypassCommand em formato de array
     * @return array
     */
    public function toArray() {
        return array(
            'name' => $this->name,
            'referentVertexIndex' => $this->referentVertexIndex,
            'openingCommandIndex' => $this->openingCommandIndex,
            'initialLineNumber' => $this->initialLineNumber,
            'endLineNumber' => $this->endLineNumber,
        );
    }
}