<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Validation;


use Zend\InputFilter\InputFilter;
use Zend\Validator\NotEmpty;

class RankValidator extends InputFilter
{
    public function __construct($data = array())
    {
        // id
        $this->add(array(
            'name'     => 'id',
            'required' => false,
            'filters'  => array(
                array('name' => 'Int'),
            ),
        ));

        // problema
        $this->add(array(
            'name'     => 'problemId',
            'required' => true,
            'filters'  => array(
                array('name' => 'Int'),
            ),
            'validators' => array(
                array(
                    'name' => NotEmpty::class,
                    'options' => array(
                        'messages' => array(
                            NotEmpty::IS_EMPTY => 'Informe o problema.',
                            NotEmpty::INVALID => 'Informe o problema.',
                        ),
                    ),
                ),
            ),
        ));

//        // usuário
//        $this->add(array(
//            'name'     => 'userId',
//            'required' => true,
//            'filters'  => array(
//                array('name' => 'Int'),
//            ),
//            'validators' => array(
//                array(
//                    'name' => NotEmpty::class,
//                    'options' => array(
//                        'messages' => array(
//                            NotEmpty::IS_EMPTY => 'Informe a Linguagem de Programação.',
//                            NotEmpty::INVALID => 'Informe a Linguagem de Programação.',
//                        ),
//                    ),
//                ),
//            ),
//        ));

        // código fonte
        $this->add(array(
            'name'     => 'sourceCodeId',
            'required' => true,
            'filters'  => array(
                array('name' => 'Int'),
            ),
            'validators' => array(
                array(
                    'name' => NotEmpty::class,
                    'options' => array(
                        'messages' => array(
                            NotEmpty::IS_EMPTY => 'Informe a Linguagem de Programação.',
                            NotEmpty::INVALID => 'Informe a Linguagem de Programação.',
                        ),
                    ),
                ),
            ),
        ));

        // média da análise
        $this->add(array(
            'name'     => 'analysisMean',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => NotEmpty::class,
                    'options' => array(
                        'messages' => array(
                            NotEmpty::IS_EMPTY => 'Informe o Código Fonte',
                            NotEmpty::INVALID => 'Informe o Código Fonte.',
                        ),
                    ),
                ),
            ),
        ));

        if (! empty($data)) {
            $this->setData($data);
        }
    }
}