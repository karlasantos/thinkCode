<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Model\Entity;


use Application\Model\Entity\Entity;
use Doctrine\DBAL\Types\JsonArrayType;
use Doctrine\ORM\Mapping as ORM;
use Zend\Json\Json;
use Zend\View\Model\JsonModel;

/**
 * Class AnalysisResults
 *  Representa os Resultados da Análise dos Códigos Fonte submetidos
 *
 * @ORM\Entity Mapeamento Objeto Relacional
 * @ORM\Table(name="analysis_results")
 * @package SourceCode\Model\Entity
 */
class AnalysisResults extends Entity
{
    /**
     * Id de identificação da Análise
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * Código fonte da análise
     *
     * @ORM\OneToOne(targetEntity="SourceCode", mappedBy="analysisResults", cascade={"persist", "remove"})
     * @var SourceCode
     */
    private $sourceCode;

    /**
     * Quantidade de linhas úteis do código fonte
     *
     * @ORM\Column(name="number_useful_lines", type="integer")
     * @var integer
     */
    private $numberUsefulLines;

    /**
     * Quantidade de variáveis no código fonte
     *
     * @ORM\Column(name="number_variables", type="integer")
     * @var integer
     */
    private $numberVariables;

    /**
     * Quantidade de conectivos lógicos no código fonte
     *
     * @ORM\Column(name="number_logical_connectives", type="integer")
     * @var integer
     */
    private $numberLogicalConnectives;

    /**
     * Quantidade de comandos de desvio do código fonte
     *
     * @ORM\Column(name="number_diversion_commands", type="integer")
     * @var integer
     */
    private $numberDiversionCommands;

    /**
     * Quantidade de regiões do grafo de fluxo
     *
     * @ORM\Column(name="number_regions_graph", type="integer")
     * @var integer
     */
    private $numberRegionsGraph;

    /**
     * Quantidade de arestas do grafo
     *
     * @ORM\Column(name="number_edges_graph", type="integer")
     * @var integer
     */
    private $numberEdgesGraph;

    /**
     * Quantidade de vértices do grafo
     *
     * @ORM\Column(name="number_vertex_graph", type="integer")
     * @var integer
     */
    private $numberVertexGraph;

    /**
     * Resultado da métrica de complexidade ciclomática
     *
     * @ORM\Column(name="cyclomatic_complexity", type="integer")
     * @var integer
     */
    private $cyclomaticComplexity;

    /**
     * Armazena o JSON do Grafo de Fluxo do código fonte
     *
     * @ORM\Column(type="json_array", nullable=false)
     *
     * @var string
     */
    private $graph;

    /**
     * Armazena a média aritmética das análises
     *
     * @ORM\Column(name="arithmetic_mean", type="float", nullable=false)
     *
     * @var float
     */
    private $arithmeticMean;

    /**
     * Retorna o Id de identificação da Análise
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Retorna o código fonte que a análise pertence
     *
     * @return SourceCode
     */
    public function getSourceCode()
    {
        return $this->sourceCode;
    }

    /**
     * Define o código fonte que a análise pertence
     *
     * @param SourceCode $sourceCode
     */
    public function setSourceCode($sourceCode)
    {
        $this->sourceCode = $sourceCode;
    }

    /**
     * Retorna a quantidade de linhas úteis do código fonte
     *
     * @return int
     */
    public function getNumberUsefulLines()
    {
        return $this->numberUsefulLines;
    }

    /**
     * Define a quantidade de linhas úteis do código fonte
     *
     * @param int $numberUsefulLines
     */
    public function setNumberUsefulLines($numberUsefulLines)
    {
        $this->numberUsefulLines = $numberUsefulLines;
    }

    /**
     * Retorna a quantidade de variáveis do código fonte
     *
     * @return int
     */
    public function getNumberVariables()
    {
        return $this->numberVariables;
    }

    /**
     * Define a quantidade de variáveis do código fonte
     *
     * @param int $numberVariables
     */
    public function setNumberVariables($numberVariables)
    {
        $this->numberVariables = $numberVariables;
    }

    /**
     * Retorna a quantidade de operadores lógicos do código fonte
     *
     * @return int
     */
    public function getNumberLogicalConnectives()
    {
        return $this->numberLogicalConnectives;
    }

    /**
     * Define a quantidade de operadores lógicos do código fonte
     *
     * @param int $numberLogicalConnectives
     */
    public function setNumberLogicalConnectives($numberLogicalConnectives)
    {
        $this->numberLogicalConnectives = $numberLogicalConnectives;
    }

    /**
     * Retorna a quantidade de comandos de desvio do código fonte
     *
     * @return int
     */
    public function getNumberDiversionCommands()
    {
        return $this->numberDiversionCommands;
    }

    /**
     * Define a quantidade de comandos de desvio do código fonte
     *
     * @param int $numberDiversionCommands
     */
    public function setNumberDiversionCommands($numberDiversionCommands)
    {
        $this->numberDiversionCommands = $numberDiversionCommands;
    }

    /**
     * Retorna a quantidade de regiões do grafo de fluxo
     *
     * @return int
     */
    public function getNumberRegionsGraph()
    {
        return $this->numberRegionsGraph;
    }

    /**
     * Define a quantidade de regiões do grafo de fluxo do código fonte
     *
     * @param int $numberRegionsGraph
     */
    public function setNumberRegionsGraph($numberRegionsGraph)
    {
        $this->numberRegionsGraph = $numberRegionsGraph;
    }

    /**
     * Retorna a quantidade de arestas do grafo de fluxo do código fonte
     *
     * @return int
     */
    public function getNumberEdgesGraph()
    {
        return $this->numberEdgesGraph;
    }

    /**
     * Define a quantidade de arestas do grafo de fluxo do código fonte
     *
     * @param int $numberEdgesGraph
     */
    public function setNumberEdgesGraph($numberEdgesGraph)
    {
        $this->numberEdgesGraph = $numberEdgesGraph;
    }

    /**
     * Retorna a quantidade de vértices do grafo de fluxo do código fonte
     *
     * @return int
     */
    public function getNumberVertexGraph()
    {
        return $this->numberVertexGraph;
    }

    /**
     * Define a quantidade de vértices do grafo de fluxo do código fonte
     *
     * @param int $numberVertexGraph
     */
    public function setNumberVertexGraph($numberVertexGraph)
    {
        $this->numberVertexGraph = $numberVertexGraph;
    }

    /**
     * Retorna o resultado da métrica de Complexidade Ciclomática do código fonte
     *
     * @return int
     */
    public function getCyclomaticComplexity()
    {
        return $this->cyclomaticComplexity;
    }

    /**
     * Define o resultado da métrica de Complexidade Ciclomática do código fonte
     *
     * @param int $cyclomaticComplexity
     */
    public function setCyclomaticComplexity($cyclomaticComplexity)
    {
        $this->cyclomaticComplexity = $cyclomaticComplexity;
    }

    /**
     * Retorna o grafo do código fonte em formato JSON
     *
     * @return string
     */
    public function getGraph()
    {
        return $this->graph;
    }

    /**
     * Define o grafo do código fonte em formato JSON
     *
     * @param string $graph
     */
    public function setGraph($graph)
    {
        $this->graph = $graph;
    }

    /**
     * Retorna a média aritmética das análises
     *
     * @return float
     */
    public function getArithmeticMean()
    {
        return $this->arithmeticMean;
    }

    /**
     * Define a média aritmética das análises
     *
     */
    public function setArithmeticMean()
    {
        $this->arithmeticMean = round(
            (($this->cyclomaticComplexity*6 +
                    $this->numberDiversionCommands*5 +
                    $this->numberRegionsGraph*4 +
                    $this->numberVariables*3 +  +
                    $this->numberLogicalConnectives*2 +
                    $this->numberUsefulLines)/21), 3);
    }

    /**
     * Método retorna os dados dos Resultados da Análise em formato de array
     * @return array
     */
    public function toArray()
    {
        return array(
            'id'                       => $this->id,
            'numberUsefulLines'        => $this->numberUsefulLines,
            'numberVariables'          => $this->numberVariables,
            'numberLogicalConnectives' => $this->numberLogicalConnectives,
            'numberDiversionCommands'  => $this->numberDiversionCommands,
            'numberRegionsGraph'       => $this->numberRegionsGraph,
            'numberEdgesGraph'         => $this->numberEdgesGraph,
            'numberVertexGraph'        => $this->numberVertexGraph,
            'cyclomaticComplexity'     => $this->cyclomaticComplexity,
            'arithmeticMean'           => $this->arithmeticMean,
            'graph'                    => (array)json_decode($this->graph),
        );
    }
}