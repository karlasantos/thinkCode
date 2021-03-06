<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Service;
use Doctrine\ORM\EntityManager;
use SourceCode\Model\Entity\AnalysisResults;
use SourceCode\Model\Vertex;
use SourceCode\Service\Language as LanguageService;
use SourceCode\Model\Entity\SourceCode as SourceCodeEntity;

/**
 * Class AnalysisStructure
 * Realiza a análise dos códigos fonte
 * @package SourceCode\Service
 */
class AnalysisStructure
{
    /**
     * Gerenciador de entidades
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Array de vértices gerados
     * @var array
     */
    protected $vertices;

    /**
     * Serviço de carregamento e verificação dos dados da linguagem
     * @var Language
     */
    protected $languageService;

    /**
     * Serviço de coleta de dados do código fonte
     *
     * @var DataCollect
     */
    protected $dataCollectService;

    /**
     * Resultados da análise
     *
     * @var AnalysisResults
     */
    protected $analysisResults;

    /**
     * AnalysisStructure constructor.
     * @param EntityManager $entityManager Gerenciador de entidades
     * @param Language $languageService Serviço de carregamento e verificação dos dados da linguage
     * @param array $vertices vértices do grafo
     * @param DataCollect $dataCollectService serviço de coleta de dados
     */
    public function __construct(EntityManager $entityManager, LanguageService $languageService, $vertices, DataCollect $dataCollectService)
    {
        $this->entityManager = $entityManager;
        $this->languageService = $languageService;
        $this->vertices = $vertices;
        $this->dataCollectService = $dataCollectService;
    }

    /**
     * Calcula a complexidade ciclomática de acordo com o código fonte enviado
     *
     * @param SourceCodeEntity $sourceCode
     * @return AnalysisResults
     */
    public function calculateCyclomaticComplexity(SourceCodeEntity $sourceCode)
    {
        $counterEdges = 0;
        $counterBypassCommand = 0;

        foreach ($this->vertices as $vertex) {
            if($vertex instanceof Vertex) {
                if ($vertex->getRightVertexIndex() != -1)
                    $counterEdges++;

                if($vertex->getLeftVertexIndex() != -1)
                    $counterEdges++;

                if($this->languageService->isInitialBypassCommand($vertex->getName()))
                    $counterBypassCommand++;
            }
        }

        $numberCyclomatic = ($counterEdges - count($this->vertices)) + 2;

        $this->analysisResults = new AnalysisResults();

        $this->analysisResults->setNumberEdgesGraph($counterEdges);
        $this->analysisResults->setNumberDiversionCommands($counterBypassCommand);
        $this->analysisResults->setNumberRegionsGraph($counterBypassCommand);
        $this->analysisResults->setNumberVertexGraph(count($this->vertices));
        $this->analysisResults->setCyclomaticComplexity($numberCyclomatic);
        $this->analysisResults->setNumberUsefulLines($this->dataCollectService->getUsefulLineCounter());
        $this->analysisResults->setNumberLogicalConnectives($this->dataCollectService->getLogicalConnectiveCounter());
        $this->analysisResults->setNumberVariables($this->dataCollectService->getVariableCounter());
        $this->analysisResults->setArithmeticMean();
        $this->analysisResults->setSourceCode($sourceCode);
        $this->entityManager->persist($this->analysisResults);

        return $this->analysisResults;
    }

    /**
     * Gera o array representando grafo em JSON de um código fonte enviado
     *
     * @param SourceCodeEntity $sourceCode
     */
    public function generateJsonGraph(SourceCodeEntity $sourceCode)
    {
        $language = $sourceCode->getLanguage();
        $verticesJson = [];
        $edgesJson = [];
        $idEdge = null;
        $target = null;

        //define o grafo em formato JSON que será entendido pela interface
        foreach ($this->vertices as $key => $vertex) {
            $idEdge = null;
            $target = null;

            if($vertex instanceof Vertex) {
                $verticesJson[$key] = [
                    'group' => 'nodes',
                    'data' => [
                        'id' => ($key),
                        'name' => $vertex->getName(),
                    ],
                    'position' => [
                        'x' => $vertex->getX(),
                        'y' => $vertex->getY()
                    ],
                ];

                if($vertex->getRightVertexIndex() != -1) {
                    $target = $this->vertices[$vertex->getRightVertexIndex()];
                    if($target instanceof Vertex && $target->getName() == $language->getEndVertexName().$vertex->getName()) {
                        $idEdge = ($vertex->getInitialLineNumber())? $vertex->getInitialLineNumber() . (($vertex->getEndLineNumber())? "..." . $vertex->getEndLineNumber() : '.') : '.';
                    }

                    $edgesJson[] = array(
                        'group' => 'edges',
                        'data' => array(
                            'id' => $key.$vertex->getRightVertexIndex().'.',
                            'label' => ($idEdge != null)? $idEdge : null,
                            'source' => ($key),
                            'target' => $vertex->getRightVertexIndex(),
                        )
                    );
                }

                if($vertex->getLeftVertexIndex() != -1) {
                    $idEdge = null;
                    $target = $this->vertices[$vertex->getLeftVertexIndex()];
                    if($target instanceof Vertex && $target->getName() == $language->getEndVertexName().$vertex->getName()) {
                        $idEdge = ($vertex->getInitialLineNumber())? $vertex->getInitialLineNumber() . (($vertex->getEndLineNumber())? "..." . $vertex->getEndLineNumber() : '.') : '.';
                    }

                    $edgesJson[] = array(
                        'group' => 'edges',
                        'data' => array(
                            'id' => $key.$vertex->getLeftVertexIndex().'.',
                            'label' => ($idEdge != null)? $idEdge : null,
                            'source' => ($key),
                            'target' => $vertex->getLeftVertexIndex(),
                        )
                    );
                }

                if(count($vertex->getMoreVertexIndexes()) > 0) {
                    foreach ($vertex->getMoreVertexIndexes() as $index) {
                        $edgesJson[] = array(
                            'group' => 'edges',
                            'data' => array(
                                'id' => $key.$index.'.',
                                'source' => ($key),
                                'target' => $index,
                            )
                        );
                    }
                }
            }
        }
//        print_r('---------------------------');
//        $arrayResult = array();
//        foreach ($this->vertices as $key => $value) {
//            if ($value instanceof Vertex) {
////                    $valor = [
////                        'name' => $value->getName(),
////                        'openingVertexIndex' => $value->getOpeningVertexIndex(),
////                        'lineNumber' => $value->getEndLineNumber()
////                    ];
////                    $setValue = $value->toArray();
////                    $setValue['id'] = $key;
////                    $arrayResult[] = $setValue;
//                $arrayResult[] = $value->toArray();
//            }
//        }
//        \Zend\Debug\Debug::dump($arrayResult);
//
//        \Zend\Debug\Debug::dump('elements');


        $elements = array(
            'elements' => array_merge($verticesJson, $edgesJson),
        );
//        \Zend\Debug\Debug::dump($elements);
//        die();


        //decodifica e salva o Grafo JSON da análise
        $this->analysisResults->setGraph(json_encode($elements));
        $this->entityManager->persist($this->analysisResults);
    }
}