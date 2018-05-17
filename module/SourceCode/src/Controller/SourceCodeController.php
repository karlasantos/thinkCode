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
use SourceCode\Entity\Language;
use SourceCode\Entity\Problem;
use SourceCode\Entity\SourceCode;
use SourceCode\Model\Vertex;
use SourceCode\Service\AnalysisStructure;
use SourceCode\Service\DataCollect;
use SourceCode\Validation\SourceCodeValidator;
use User\Entity\User;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class SourceCodeController
 * Controller de código fonte, responsável pela submissão de um código fonte e listagem dos códigos submetidos
 * @package SourceCode\Controller
 */
class SourceCodeController extends RestfulController
{
    /**
     * Service de coleta de dados do código fonte
     *
     * @var DataCollect
     */
    private $dataCollect;

    /**
     * @var AnalysisStructure
     */
    private $analyses;

    /**
     * Construtor da classe
     * SourceCodeController constructor.
     * @param EntityManager $entityManager
     * @param DataCollect $dataCollect
     */
    public function __construct(EntityManager $entityManager, DataCollect $dataCollect)
    {
        parent::__construct($entityManager);
        //todo colocar analyses
        $this->dataCollect = $dataCollect;
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
            $analysis = new AnalysisStructure($this->entityManager);
            $language = $this->entityManager->find(Language::class, 1);
            $sourceCode = new SourceCode();
            if($language instanceof Language)
                $sourceCode->setLanguage($language);
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
            $sourceCode->setContent("int main() {
            \ndo
            \n{
                
            \n} while (b > 0);
            }");
            $sourceCode->setContent("int main() {
            \nwhile (b > 0)
            \n{
                
            \n}
            }");
            $sourceCode->setContent("int main() {
                
                    if {
                    } else if {
                       for () {
                       }
                    } else {
                    }
                    
            }");
            $sourceCode->setContent("int main() {
                
                    if {
                        for () {
                        }
                    } else {
                    }
                   
                    
            }");
//            $result = $this->dataCollect->getDataFromCode($sourceCode);

            //estrutura de analise
            $result = $analysis->setVertices($sourceCode);
            $result = $analysis->setEdges($sourceCode->getLanguage());
            $result = $analysis->setCoordinates($sourceCode->getLanguage());
            $arrayResult = array();
            foreach ($result as $value) {
                if($value instanceof Vertex)
//                    $valor = [
//                        'name' => $value->getName(),
//                        'openingVertexIndex' => $value->getOpeningVertexIndex(),
//                        'lineNumber' => $value->getEndLineNumber()
//                    ];
                    $arrayResult[] = $value->toArray();
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
            $user = $this->entityManager->find(User::class, $userId);
            $language = $this->entityManager->find(Language::class, $sourceCodeFilter->getValue('languageId'));
            $problem = $this->entityManager->find(Problem::class, $sourceCodeFilter->getValue('problemId'));

            if($user instanceof User)
                $sourceCode->setUser($user);

            if($language instanceof Language)
                $sourceCode->setLanguage($language);

            if($problem instanceof Problem)
                $sourceCode->setProblem($problem);

            $sourceCode->setSubmissionDate();

            //todo enviar o id do código a ser comparado no $data da requisição
            \Zend\Debug\Debug::dump(strpos($sourceCode->getContent(), PHP_EOL));
            die();
            $this->entityManager->persist($sourceCode);
            $this->entityManager->flush();
        } catch (Exception $exception) {

        }

        return parent::create($data); // TODO: Change the autogenerated stub
    }
}