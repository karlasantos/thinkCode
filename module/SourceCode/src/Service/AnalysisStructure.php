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
    protected $entityManager;

    protected $vertices;

    protected $languageService;

    protected $dataCollectService;

    protected $analysisResults;

    public function __construct(EntityManager $entityManager, LanguageService $languageService, $vertices, DataCollect $dataCollectService)
    {
        $this->entityManager = $entityManager;
        $this->languageService = $languageService;
        $this->vertices = $vertices;
        $this->dataCollectService = $dataCollectService;
    }

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
                        'id' => ($key+1),
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
                        $idEdge = ($vertex->getInitialLineNumber())? $vertex->getInitialLineNumber() . (($vertex->getEndLineNumber())? "..." . $vertex->getEndLineNumber() : '') : '';
                    }

                    $edgesJson[] = array(
                        'group' => 'edges',
                        'data' => array(
                            'id' => $idEdge,
                            'source' => ($key+1),
                            'target' => $vertex->getRightVertexIndex()+1,
                        )
                    );
                }

                if($vertex->getLeftVertexIndex() != -1) {
                    $idEdge = null;
                    $target = $this->vertices[$vertex->getLeftVertexIndex()];
                    if($target instanceof Vertex && $target->getName() == $language->getEndVertexName().$vertex->getName()) {
                        $idEdge = ($vertex->getInitialLineNumber())? $vertex->getInitialLineNumber() . (($vertex->getEndLineNumber())? "..." . $vertex->getEndLineNumber() : '') : '';
                    }

                    $edgesJson[] = array(
                        'group' => 'edges',
                        'data' => array(
                            'id' => $idEdge,
                            'source' => ($key+1),
                            'target' => $vertex->getLeftVertexIndex()+1,
                        )
                    );
                }

                if(count($vertex->getMoreVertexIndexes()) > 0) {
                    foreach ($vertex->getMoreVertexIndexes() as $index) {
                        $edgesJson[] = array(
                            'group' => 'edges',
                            'data' => array(
                                'source' => ($key+1),
                                'target' => $index+1,
                            )
                        );
                    }
                }
            }
        }

        $elements = array(
            'elements' => array_merge($verticesJson, $edgesJson),
        );

        //decodifica e salva o Grafo JSON da análise
        $this->analysisResults->setGraph(json_encode($elements));
        $this->entityManager->persist($this->analysisResults);
    }
}