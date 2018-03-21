<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace User\Validation;


use Zend\InputFilter\InputFilter;
use Zend\Validator\EmailAddress;
use Zend\Validator\Identical;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

/**
 * Valida os dados enviados na requisição de criação de Usuário
 *
 * Class CreateUserValidator
 * @package User\Validation
 */
class CreateUserValidator extends InputFilter
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

        // email
        $this->add(array(
            'name'     => 'email',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            NotEmpty::IS_EMPTY => 'Informe o e-mail',
                            NotEmpty::INVALID => 'Informe o e-mail',
                        ),
                    ),
                ),
                array(
                    'name' => 'EmailAddress',
                    'options' => array(
                        'messages' => array(
                            EmailAddress::INVALID          => 'E-mail inválido',
                            EmailAddress::INVALID_FORMAT   => 'E-mail inválido',
                            EmailAddress::INVALID_HOSTNAME => 'E-mail inválido',
                        )
                    )
                )
            ),
        ));

        // senha
        $this->add(array(
            'name'     => 'password',
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
                            NotEmpty::IS_EMPTY => 'Informe a senha',
                            NotEmpty::INVALID => 'Informe a senha',
                        ),
                    ),
                ),
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 8,
                        'max' => 55,
                        'messages' => array(
                            StringLength::TOO_SHORT => 'A senha conter no mínimo 6 caracteres.',
                            StringLength::TOO_LONG => 'A senha conter no máximo 40 caracteres.',
                        ),
                    ),
                )
            ),
        ));

        // confirmacao de senha
        $this->add(array(
            'name'     => 'passwordConfirm',
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
                            NotEmpty::IS_EMPTY => 'Confirme a senha',
                            NotEmpty::INVALID => 'Confirme a senha',
                        ),
                    ),
                ),
                array(
                    'name' => 'Identical',
                    'options' => array(
                        'token' => 'password',
                        'messages' => array(
                            Identical::NOT_SAME      => 'As senhas não são idênticas',
                            Identical::MISSING_TOKEN => 'As senhas não são idênticas',
                        ),
                    ),
                )
            ),
        ));

        if (! empty($data)) {
            $this->setData($data);
        }
    }

}