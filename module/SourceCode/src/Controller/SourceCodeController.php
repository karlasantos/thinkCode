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
use SourceCode\Entity\Language;
use SourceCode\Entity\Problem;
use SourceCode\Entity\SourceCode;
use SourceCode\Model\CodeBypassCommand;
use SourceCode\Model\Vertex;
use SourceCode\Service\AnalysisStructure;
use SourceCode\Service\DataCollect;
use SourceCode\Service\GraphStructure;
use SourceCode\Validation\SourceCodeValidator;
use Symfony\Component\Debug\Tests\FatalErrorHandler\UndefinedMethodFatalErrorHandlerTest;
use User\Controller\UserController;
use User\Entity\User;
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
            $language = $this->entityManager->find(Language::class, 1);
            if($language instanceof Language)
                $sourceCode->setLanguage($language);

            $languageService = new \SourceCode\Service\Language($this->entityManager);
            //busca e define os elementos da linguagem do banco de dados
            $languageService->searchElementsOfLanguage($language->getId());
            $dataCollect = new DataCollect($this->entityManager, $languageService);
            $analysis = new GraphStructure($this->entityManager, $dataCollect);

            $sourceCode->setContent("int main() {
            \nint a = 1, c;
            \nfloat b = 0;
            \nif(a > 0 || b == 0)
            \n {
               \nif(b >0)
               \n{
                  \nb=1;
               \n}
               \na++;
            \n}
            \n//teste
            \nelse {
               \na--;
            \n}
            \ndo {
                \nif() 
                \n{
                \n}
            \n}
            \nwhile (a > 1 && a == 2);
            \nswitch (a) 
            \n{
                \ncase 1:
                   \nif (a)
                   \n{
                   \n}
                  \nbreak;
                \ncase 2: 
                  \nif 
                  \n{
                  \n}
                  \nbreak; 
                \ndefault:
                  \nif () 
                  \n{
                  \n}
                  \nelse 
                  \n{
                      \nif ()
                  \n}
            \n}
            \nfor(i=0; i<3; i++)
            \n{
            \n}
            \nswitch (b) 
            \n{
                \ncase 1:
                   \nbreak;
            \n}
            \n}");

            $sourceCode->setContent("int main() {
            \nint a = 1, c;
            \nfloat b = 0;
            \nif(a > 0 || b == 0)
            \n {
               \nif(b >0)
               \n{
                  \nb=1;
               \n}
               \na++;
            \n}
            \nelse 
               \nif () {
            \n}
           
            }");
            $sourceCode->setContent("int main() {
            \nswitch (b) 
            \n{
                \ncase 1:
                    if () {
                    }
                   \nbreak;
                case 2:
            \n}
            }");
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
            $result = $dataCollect->getDataFromCode($sourceCode);
            $userId =  $_SESSION['Zend_Auth']->getArrayCopy()['storage']['id'];
            $user = $this->entityManager->find(User::class, $userId);
            $problem = $this->entityManager->find(Problem::class, 1);

            //estrutura de analise
            $result = $analysis->setGraphData($sourceCode);
            $analysisStructures = new AnalysisStructure($this->entityManager, $languageService, $result, $dataCollect);
            $resultObject = $analysisStructures->calculateCyclomaticComplexity($sourceCode);
            //como salvar em formato JSON
            $graphJSON = $analysisStructures->generateJsonGraph($sourceCode);

            $sourceCode->setUser($user);
            $sourceCode->setAnalysisResults($resultObject);
            $sourceCode->setProblem($problem);
//            $sourceCode->setReferential(false);
            $this->entityManager->persist($sourceCode);
            $this->entityManager->persist($resultObject);
//            $this->entityManager->flush();

            //como retornar em formato JSON
            return new JsonModel((array)json_decode($resultObject->getGraph()));

            die();
            $arrayResult = array();
            foreach ($result as $key => $value) {
                if($value instanceof Vertex)
//                    $valor = [
//                        'name' => $value->getName(),
//                        'openingVertexIndex' => $value->getOpeningVertexIndex(),
//                        'lineNumber' => $value->getEndLineNumber()
//                    ];
                    $setValue = $value->toArray();
                    $setValue['id'] = $key;
                    $arrayResult[] = $setValue;
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
        $sourceCode = new SourceCode();
        $userId =  $_SESSION['Zend_Auth']->getArrayCopy()['storage']['id'];

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

        $sourceCode->setData($sourceCodeFilter->getValues());

        try {
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

            if(isset($data['userCompareId']) && !empty($data['userCompareId'])) {
                $sourceCodeSystem = $this->entityManager->getRepository(SourceCode::class)->findOneBy(
                    array(
                        'problem' => $problem->getId(),
                        'user'    => $data['userCompareId']
                    )
                );

                if(!$sourceCodeSystem instanceof SourceCode)
                    throw new Exception("O código selecionado para comparação não foi encontrado.");

                //retorna o resultado da análise do código fonte selecionado pelo usuário
                $analysisResultsSystem = $sourceCodeSystem->getAnalysisResults()->toArray();
            }

            $results = array(
                'result' => array(
                    'sourceCodeUser' => array(
                        'analysisResults' => $analysisResultsReturn,
                        'content' => $sourceCode->getContent()
                    ),
                    'sourceCodeSystem' => array(
                        'analysisResults' => $analysisResultsSystem,
                        'userCompareId' => (isset($data['userCompareId']) && !empty($data['userCompareId']))? $data['userCompareId'] : null,
                    ),
                )
            );

            //todo definir o ranking
        } catch (Exception $exception) {
            $this->getResponse()->setStatusCode(400);
            $results = array(
                'result' => $exception->getMessage(),
            );
        }

        return new JsonModel($results);
    }
}