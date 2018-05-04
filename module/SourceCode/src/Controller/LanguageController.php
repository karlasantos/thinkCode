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
            $sourceCode->setContent("int main() {\nint a = 1, c;\nfloat b = 0;\nif(a > 0 || b == 0)\n {\nif(b >0)\n{\nb=1;\n}\na++;\n}\n//teste\nelse {\na--;\n}\ndo {\nif() \n{\n}\n}\nwhile (a > 1 && a == 2);\nswitch (a) \n{\ncase 1:\nif (a)\n{\n}\nbreak;\ncase 2: \nif \n{\n}\nbreak; \ndefault:\nif () \n{\n}\nelse \n{\n}\n}\nfor(i=0; i<3; i++)\n{\n}switch (b) \n{\ncase 1: if (c) \nelse\nif (a) \nbreak;\n}\n}");
            $result = $this->dataCollect->getDataFromCode($sourceCode);
            $arrayResult = array();
            foreach ($result as $value) {
                if($value instanceof CodeBypassCommand)
                    $arrayResult[] = $value->getName();
            }
           // die();
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