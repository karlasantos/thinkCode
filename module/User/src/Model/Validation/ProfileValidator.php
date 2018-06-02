<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace User\Model\Validation;

use Zend\InputFilter\InputFilter;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

/**
 * Class ProfileValidator
 * Validador de dados do perfil do usuário
 * @package User\Model\Validation
 */
class ProfileValidator extends InputFilter
{
    /**
     * ProfileValidator constructor.
     * Realiza a validação dos dados e define as mensagens de erro em caso de dados preenchidos incorretamente
     * @param array $data dados enviados
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

        // fullName
        $this->add(array(
            'name'     => 'fullName',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            NotEmpty::IS_EMPTY => 'Informe o nome',
                            NotEmpty::INVALID => 'Informe o nome',
                        ),
                    ),
                ),
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 3,
                        'max' => 255,
                        'messages' => array(
                            StringLength::TOO_SHORT => 'O nome deve conter no mínimo 3 caracteres.',
                            StringLength::TOO_LONG => 'O nome deve conter no máximo 255 caracteres.',
                        ),
                    ),
                )
            ),
        ));

        // birthday
        $this->add(array(
            'name'     => 'birthday',
            'required' => false,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 10,
                        'max' => 10,
                        'messages' => array(
                            StringLength::TOO_SHORT => 'A data de nascimento deve conter 10 caracteres.',
                            StringLength::TOO_LONG => 'A data de nascimento deve conter 10 caracteres.',
                        ),
                    ),
                )
            ),
        ));

        // school
        $this->add(array(
            'name'     => 'school',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            NotEmpty::IS_EMPTY => 'Informe o curso',
                            NotEmpty::INVALID => 'Informe o curso',
                        ),
                    ),
                ),
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 2,
                        'max' => 255,
                        'messages' => array(
                            StringLength::TOO_SHORT => 'O curso deve conter no mínimo 2 caracteres.',
                            StringLength::TOO_LONG => 'O curso deve conter no máximo 255 caracteres.',
                        ),
                    ),
                )
            ),
        ));

        // gender
        $this->add(array(
            'name'     => 'gender',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            NotEmpty::IS_EMPTY => 'Selecione o gênero',
                            NotEmpty::INVALID => 'Selecione o gênero',
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