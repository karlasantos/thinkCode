<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Controller;


use Doctrine\ORM\EntityManager;
use SourceCode\Entity\Language;
use SourceCode\Entity\SourceCode;
use SourceCode\Model\CodeBypassCommand;
use SourceCode\Service\DataCollect;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class LanguageController  extends AbstractRestfulController
{
    protected $entityManager;

    /**
     * @var DataCollect
     */
    private $dataCollect;

    public function __construct(EntityManager $entityManager, DataCollect $dataCollect)
    {
        $this->entityManager = $entityManager;
        $this->dataCollect = $dataCollect;
    }

    public function getList()
    {
        try {
            $language = $this->entityManager->find('SourceCode\Entity\Language', 1);
            $sourceCode = new SourceCode();
            if($language instanceof Language)
                $sourceCode->setLanguage($language);
            $sourceCode->setContent("int main() {\nint a = 1;\nif(a > 0)\n {\na++;\n}else \n{\na--;\n}\n}");
            $result = $this->dataCollect->getDataFromCode($sourceCode);
            $arrayResult = array();
            foreach ($result as $value) {
                if($value instanceof CodeBypassCommand)
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