<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Controller;


use Application\Controller\RestfulController;
use Doctrine\DBAL\Types\JsonArrayType;
use Doctrine\ORM\EntityManager;
use Exception;
use SourceCode\Model\Entity\Language;
use SourceCode\Model\Entity\Problem;
use SourceCode\Model\Entity\SourceCode;
use SourceCode\Model\CodeBypassCommand;
use SourceCode\Model\Vertex;
use SourceCode\Service\AnalysisStructure;
use SourceCode\Service\DataCollect;
use SourceCode\Service\GraphStructure;
use SourceCode\Service\Rank;
use SourceCode\Validation\SourceCodeValidator;
use Symfony\Component\Debug\Tests\FatalErrorHandler\UndefinedMethodFatalErrorHandlerTest;
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
     * @param DataCollect $dataCollect
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

    public function getList()
    {
        //die("teste");
        try {
            $sourceCode = new SourceCode();
            $language = $this->entityManager->find(Language::class, 2);
            if($language instanceof Language)
                $sourceCode->setLanguage($language);

            $languageService = new \SourceCode\Service\Language($this->entityManager);
            //busca e define os elementos da linguagem do banco de dados
            $languageService->searchElementsOfLanguage($language->getId());
            $dataCollect = new DataCollect($this->entityManager, $languageService);
            $analysis = new GraphStructure($this->entityManager, $dataCollect);

//            $sourceCode->setContent("int main() {
//            \nint a = 1, c;
//            \nfloat b = 0;
//            \nif(a > 0 || b == 0)
//            \n {
//               \nif(b >0)
//               \n{
//                  \nb=1;
//               \n}
//               \na++;
//            \n}
//            \n//teste
//            \nelse {
//               \na--;
//            \n}
//            \ndo {
//                \nif()
//                \n{
//                \n}
//            \n}
//            \nwhile (a > 1 && a == 2);
//            \nswitch (a)
//            \n{
//                \ncase 1:
//                   \nif (a)
//                   \n{
//                   \n}
//                  \nbreak;
//                \ncase 2:
//                  \nif
//                  \n{
//                  \n}
//                  \nbreak;
//                \ndefault:
//                  \nif ()
//                  \n{
//                  \n}
//                  \nelse
//                  \n{
//                      \nif ()
//                  \n}
//            \n}
//            \nfor(i=0; i<3; i++)
//            \n{
//            \n}
//            \nswitch (b)
//            \n{
//                \ncase 1:
//                   \nbreak;
//            \n}
//            \n}");
//
//            $sourceCode->setContent("int main() {
//            \nint a = 1, c;
//            \nfloat b = 0;
//            \nif(a > 0 || b == 0)
//            \n {
//               \nif(b >0)
//               \n{
//                  \nb=1;
//               \n}
//               \na++;
//            \n}
//            \nelse
//               \nif () {
//            \n}
//
//            }");
//            $sourceCode->setContent("int main() {
//            \ndo
//            \n{
//
//            \n} while (b > 0);
//            }");
//            $sourceCode->setContent("int main() {
//            \nwhile (b > 0)
//            \n{
//
//            \n}
//            }");
//            $sourceCode->setContent("int main() {
//
//                    if {
//                    } else if {
//                       for () {
//                       }
//                    } else {
//                    }
//
//            }");
//            $sourceCode->setContent("
//            int main() {\n
//                if {\n
//                   do { \n
//                      while { \n
//                        if {\n
//                        }\n
//                        else {\n
//                        }\n
//                      } \n
//                   } while ();\n
//                } \n
//            }");
            $sourceCode->setContent("inicio
                \ninteiro n,fat
                \nfat<-1
                \nler n
                \nenquanto (n>=1) faz
                    \nfat<-fat*n
                    \nn<-n-1
                \nfimenquanto
            \nfim ");
            $result = $dataCollect->getDataFromCode($sourceCode);
//            $userId =  $_SESSION['Zend_Auth']->getArrayCopy()['storage']['id'];
//            $user = $this->entityManager->find(User::class, $userId);
//            $problem = $this->entityManager->find(Problem::class, 1);

            //estrutura de analise
//            $result = $analysis->setGraphData($sourceCode);
//            $analysisStructures = new AnalysisStructure($this->entityManager, $languageService, $result, $dataCollect);
//            $resultObject = $analysisStructures->calculateCyclomaticComplexity($sourceCode);
            //como salvar em formato JSON
//            $graphJSON = $analysisStructures->generateJsonGraph($sourceCode);

//            $sourceCode->setUser($user);
//            $sourceCode->setAnalysisResults($resultObject);
//            $sourceCode->setProblem($problem);
//            $sourceCode->setReferential(false);
//            $this->entityManager->persist($sourceCode);
//            $this->entityManager->persist($resultObject);
//            $this->entityManager->flush();

            //como retornar em formato JSON
//            return new JsonModel((array)json_decode($resultObject->getGraph()));

            $arrayResult = array();
            foreach ($result as $key => $value) {
                if ($value instanceof CodeBypassCommand) {
//                    $valor = [
//                        'name' => $value->getName(),
//                        'openingVertexIndex' => $value->getOpeningVertexIndex(),
//                        'lineNumber' => $value->getEndLineNumber()
//                    ];
//                    $setValue = $value->toArray();
//                    $setValue['id'] = $key;
//                    $arrayResult[] = $setValue;
                    $arrayResult[] = $value->getName();
                }
            }
            //die();
            return new JsonModel([
                'resultsC' => array($arrayResult),
            ]);
        } catch(Exception $e) {
            return new JsonModel([
                'resultsERRR' => array($e->getMessage()),
            ]);
        }

        return new JsonModel([
            'resultsL' => array($result),
        ]);
    }

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
                'result' => $exception->getMessage(),
            );
        }

        return new JsonModel($results);
    }
}