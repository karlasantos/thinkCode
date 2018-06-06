<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Controller;


use Application\Controller\RestfulController;
use Doctrine\ORM\EntityManager;
use Exception;
use SourceCode\Model\Entity\AnalysisResults;
use SourceCode\Model\Entity\Language;
use SourceCode\Model\Entity\Problem;
use SourceCode\Model\Entity\SourceCode;
use SourceCode\Service\AnalysisStructure;
use SourceCode\Service\DataCollect;
use SourceCode\Service\GraphStructure;
use SourceCode\Service\Rank;
use SourceCode\Model\Validation\SourceCodeValidator;
use User\Controller\UserController;
use User\Model\Entity\User;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use SourceCode\Service\Language as LanguageService;

/**
 * Class SourceCodeController
 * Controller de código fonte, responsável pela submissão de um código fonte e listagem dos códigos submetidos
 * @package SourceCode\Controller
 */
class SourceCodeController extends RestfulController
{
    /**
     * Construtor da classe
     * SourceCodeController constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager);
    }

    /**
     * Retorna a interface de submissão de código fonte
     * @return ViewModel
     */
    public function submissionAction()
    {
        $problemId  = $this->params()->fromQuery('problemId');
        return new ViewModel(array('problemId' => $problemId));
    }

    /**
     * Retorna a interface de resultados de submissão do código fonte
     * @return ViewModel
     */
    public function resultsAction()
    {
        $problemId = null;
        $problemId  = $this->params()->fromQuery('problemId');
        return new ViewModel(array('problemId' => $problemId));
    }

    /**
     * Retorna os dados de um código fonte submetido
     *
     * @api
     * @param integer $id Id de identificação do código fonte
     * @return mixed|JsonModel
     *
     */
    public function get($id)
    {
        $problemId = (int) $id;
        $userId =  $_SESSION['Zend_Auth']->getArrayCopy()['storage']['id'];

        try {
            $sourceCode = $this->entityManager->createQueryBuilder()
                        ->select('sc, analysisResults, ranking')
                        ->from(SourceCode::class, 'sc')
                        ->leftJoin('sc.analysisResults', 'analysisResults')
                        ->leftJoin('sc.ranking', 'ranking')
                        ->where('sc.problem = :problemId')
                        ->andWhere('sc.user = :userId')
                        ->setParameter('problemId', $problemId)
                        ->setParameter('userId', $userId)
                        ->getQuery()
                        ->getArrayResult();

            if(count($sourceCode) > 0) {
                $sourceCode = $sourceCode[0];
                $sourceCode['submissionDate'] =  $sourceCode['submissionDate'] ->format('d/m/Y');
                $sourceCode['analysisResults']['graph'] =  (array)json_decode($sourceCode['analysisResults']['graph']);
                $sourceCode['analysisResults']['analysisResultsId'] =  $sourceCode['analysisResults']['id'];
                unset($sourceCode['analysisResults']['id']);
                $sourceCode = array_merge($sourceCode, $sourceCode['analysisResults']);
                unset($sourceCode['analysisResults']);
            }
            else {
                throw new Exception("Nenhum problema encontrado correspondente.");
            }
        }
        catch (Exception $exception) {
            $this->getResponse()->setStatusCode(400);
            $sourceCode = array(
                'result' => $exception->getMessage(),
            );
        }

        return new JsonModel(array('result' => $sourceCode));
    }

    /**
     * Salva um novo códifo fonte, gera sua análise e retorna os resultados
     *
     * @api
     * @param array $data dados da submissão do código fonte
     * @return mixed|JsonModel
     */
    public function create($data)
    {
        $analysisResultsSystem = array();
        $userId =  $_SESSION['Zend_Auth']->getArrayCopy()['storage']['id'];
        $sourceCodeSystem = null;

        $sourceCodeFilter = new SourceCodeValidator($data);

        if(!$sourceCodeFilter->isValid()) {
            $this->getResponse()->setStatusCode(400);

            $messages = array();
            //monta as mensagens de erro do usuário
            foreach ($sourceCodeFilter->getMessages() as $message) {
                if(count($message) > 0) {
                    $messages[] = array_shift($message);
                }
            }

            return new JsonModel(
                array(
                    'result' => $messages
                )
            );
        }

        try {

            //busca o código fonte já inserido pelo usuário para atualizar os dados
            $sourceCode = $this->entityManager->getRepository(SourceCode::class)->findOneBy(
                array(
                    'user' => $userId,
                    'problem' => $sourceCodeFilter->getValue('problemId')
                )
            );

            //se não tiver nenhum código fonte, insere um novo
            if(!$sourceCode instanceof SourceCode) {
                $sourceCode = new SourceCode();
            } else {
                $analysisResultsRemove = $sourceCode->getAnalysisResults();

                if($analysisResultsRemove instanceof AnalysisResults) {
                    $this->entityManager->remove($analysisResultsRemove);
                }
            }

            $sourceCode->setData($sourceCodeFilter->getValues());
            $this->entityManager->beginTransaction();
            $user = $this->entityManager->find(User::class, $userId);
            $language = $this->entityManager->find(Language::class, $sourceCodeFilter->getValue('languageId'));
            $problem = $this->entityManager->find(Problem::class, $sourceCodeFilter->getValue('problemId'));

            if(!$user instanceof User)
                throw new Exception(UserController::USER_NOT_FOUND);


            if(!$language instanceof Language)
                throw new Exception("Linguagem de programação não encontrada");


            if(!$problem instanceof Problem)
                throw new Exception("Problema não encontrado");

            $sourceCode->setLanguage($language);
            $sourceCode->setProblem($problem);
            $sourceCode->setUser($user);
            $this->entityManager->persist($sourceCode);

            //serviço da linguagem
            $languageService = new LanguageService($this->entityManager);
            //busca e define os elementos da linguagem do banco de dados
            $languageService->searchElementsOfLanguage($language->getId());

            //serviço de coleta de dados do código fonte
            $dataCollect = new DataCollect($this->entityManager, $languageService);
            //retira os comandos de desvio do código fonte
            $codeComands = $dataCollect->getDataFromCode($sourceCode);

            //instancia o serviço de geração da estrutura do grafo e
            $graphStructure = new GraphStructure($this->entityManager, $dataCollect);
            //define os dados do grafo de fluxo e retorna um array de vértices com dados de arestas e coordenadas
            $vertices = $graphStructure->setGraphData($sourceCode);

            //serviço de construção da análise
            $analysisStructure = new AnalysisStructure($this->entityManager, $languageService, $vertices, $dataCollect);
            //calcula a complexidade ciclomática
            $analysisResults = $analysisStructure->calculateCyclomaticComplexity($sourceCode);
            //define o grafo de fluxo em formato JSON da API Cytoscape
            $analysisStructure->generateJsonGraph($sourceCode);

            //persiste os dados da análise
            $this->entityManager->persist($analysisResults);
            $sourceCode->setAnalysisResults($analysisResults);

            $this->entityManager->flush();
            $this->entityManager->commit();

            //retorna os dados da análise do código do usuário em formato de array
            $analysisResultsReturn = $analysisResults->toArray();

            if(isset($data['sourceCodeCompareId']) && !empty($data['sourceCodeCompareId'])) {
                $sourceCodeSystem = $this->entityManager->find(SourceCode::class, $data['sourceCodeCompareId']);

                if(!$sourceCodeSystem instanceof SourceCode)
                    throw new Exception("O código selecionado para comparação não foi encontrado.");

                //retorna o resultado da análise do código fonte selecionado pelo usuário
                $analysisResultsSystem = $sourceCodeSystem->getAnalysisResults()->toArray();
            }

            $dataRank = [
                'problemId' => $sourceCode->getProblem()->getId(),
                'sourceCodeId' => $sourceCode->getId(),
                'analysisMean' => $analysisResults->getArithmeticMean(),
            ];

            $rankService = new Rank($this->entityManager);
            $ranking = $rankService->updateRank($dataRank, $sourceCode);

            $analysisResultsReturn['content'] = $sourceCode->getContent();
            $analysisResultsReturn['ranking'] = $ranking;

            $analysisResultsSystem['userCompareId'] = ($sourceCodeSystem instanceof SourceCode)? $sourceCodeSystem->getUser()->getId() : null;
            $analysisResultsSystem['userCompare'] = ($sourceCodeSystem instanceof SourceCode)? $sourceCodeSystem->getUser()->getProfile()->getFullName() : null;

            $results = array(
                'result' => array(
                    'sourceCodeUser' => $analysisResultsReturn,
                    'sourceCodeSystem' => $analysisResultsSystem,
                )
            );


        } catch (Exception $exception) {
            $this->getResponse()->setStatusCode(400);
            $results = array(
                'result' => [
                    0 => "Ocorreu um erro interno ao processar essa solução",
                    'exception' => $exception->getMessage()
                ],
            );
        }

        return new JsonModel($results);
    }
}