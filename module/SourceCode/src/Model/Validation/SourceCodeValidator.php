<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Model\Validation;

use Zend\InputFilter\InputFilter;
use Zend\Validator\NotEmpty;

/**
 * Class SourceCodeValidator
 * Validador dos dados do Código Fonte
 * @package SourceCode\Model\Validation
 */
class SourceCodeValidator extends InputFilter
{
    /**
     * SourceCodeValidator constructor.
     * @param array $data
     */
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
                            NotEmpty::IS_EMPTY => 'Informe o problema',
                            NotEmpty::INVALID => 'Informe o problema',
                        ),
                    ),
                ),
            ),
        ));

        // linguagem de programação
        $this->add(array(
            'name'     => 'languageId',
            'required' => true,
            'filters'  => array(
                array('name' => 'Int'),
            ),
            'validators' => array(
                array(
                    'name' => NotEmpty::class,
                    'options' => array(
                        'messages' => array(
                            NotEmpty::IS_EMPTY => 'Informe a Linguagem de Programação',
                            NotEmpty::INVALID => 'Informe a Linguagem de Programação',
                        ),
                    ),
                ),
            ),
        ));

        // código de programação
        $this->add(array(
            'name'     => 'content',
            'required' => true,
            'filters'  => array(
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => NotEmpty::class,
                    'options' => array(
                        'messages' => array(
                            NotEmpty::IS_EMPTY => 'Informe o Código Fonte',
                            NotEmpty::INVALID => 'Informe o Código Fonte',
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