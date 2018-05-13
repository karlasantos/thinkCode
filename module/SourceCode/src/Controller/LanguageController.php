<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Controller;


use Application\Controller\RestfulController;
use Doctrine\ORM\EntityManager;
use SourceCode\Entity\Language;
use SourceCode\Entity\SourceCode;
use SourceCode\Model\CodeBypassCommand;
use SourceCode\Model\Vertex;
use SourceCode\Service\AnalysisStructure;
use SourceCode\Service\DataCollect;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

/**
 * Class LanguageController
 * Controller de Linguagem de Programação, responsável pela listagem das linguagens cadastradas
 * @package SourceCode\Controller
 */
class LanguageController extends RestfulController
{
    /**
     * @var DataCollect
     */
    private $dataCollect;

    public function __construct(EntityManager $entityManager, DataCollect $dataCollect)
    {
        parent::__construct($entityManager);
        $this->dataCollect = $dataCollect;
    }

    /**
     * Retorna todas as linguagens de programação cadastradas
     *
     * @return mixed|JsonModel
     */
    public function getList()
    {
        //página selecionada
        $search  = $this->params()->fromQuery('search');

        $languages = $this->entityManager->createQueryBuilder()
                    ->select('partial languages.{id, name}')
                    ->from(Language::class, 'languages');

        if($search) {
            $languages->andWhere('languages.name = :languageName')
                ->setParameter('languageName', $search);
        }

        $languages = $languages->getQuery()
            ->getArrayResult();

        return new JsonModel(array(
            'results' => $languages,
            'total' => count($languages),
        ));
    }

    public function get($id)
    {
        try {
            $analysis = new AnalysisStructure($this->entityManager);
            $language = $this->entityManager->find('SourceCode\Entity\Language', 1);
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
//            $result = $this->dataCollect->getDataFromCode($sourceCode);

            //estrutura de analise
            $result = $analysis->setVertices($sourceCode);
            $result = $analysis->setEdges($sourceCode->getLanguage());
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
//            die();
            return new JsonModel([
                'resultsC' => array($arrayResult),
            ]);
        } catch(\Exception $e) {
            return new JsonModel([
                'resultsERRR' => array($e->getMessage()),
            ]);
        }

        return new JsonModel([
            'resultsL' => array($result),
        ]);
    }
}