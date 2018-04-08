<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace User\Validation;


use User\Entity\User;
use Zend\InputFilter\InputFilter;
use Zend\Validator\EmailAddress;
use Zend\Validator\Identical;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;
use DoctrineModule\Validator\NoObjectExists;

/**
 * Valida os dados enviados na requisição de criação de Usuário
 *
 * Class UserValidator
 * @package User\Validation
 */
class UserValidator extends InputFilter
{
    public function __construct($entityManager, $data, $createRequest = false)
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
                    'name' => NotEmpty::class,
                    'options' => array(
                        'messages' => array(
                            NotEmpty::IS_EMPTY => 'Informe o e-mail',
                            NotEmpty::INVALID => 'Informe o e-mail',
                        ),
                    ),
                ),
                array(
                    'name' => EmailAddress::class,
                    'options' => array(
                        'messages' => array(
                            EmailAddress::INVALID          => 'E-mail inválido',
                            EmailAddress::INVALID_FORMAT   => 'E-mail inválido',
                            EmailAddress::INVALID_HOSTNAME => 'E-mail inválido',
                        )
                    )
                ),
                array(
                    'name' => NoObjectExists::class,
                    'options' => array(
                        'object_repository' => $entityManager->getRepository(
                            User::class
                        ),
                        'fields' => 'email',
                        'messages' => array(
                            NoObjectExists::ERROR_OBJECT_FOUND => 'E-mail já cadastrado.'
                        )
                    )
                )
            ),
        ));

        // password
        $this->add(array(
            'name'     => 'password',
            'required' => false,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => NotEmpty::class,
                    'options' => array(
                        'messages' => array(
                            NotEmpty::IS_EMPTY => 'Informe a senha',
                            NotEmpty::INVALID => 'Informe a senha',
                        ),
                    ),
                ),
                array(
                    'name' => StringLength::class,
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 6,
                        'max' => 40,
                        'messages' => array(
                            StringLength::TOO_SHORT => 'A senha conter no mínimo 6 caracteres.',
                            StringLength::TOO_LONG => 'A senha conter no máximo 40 caracteres.',
                        ),
                    ),
                )
            ),
        ));

        // passwordConfirm
        $this->add(array(
            'name'     => 'passwordConfirm',
            'required' => false,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => NotEmpty::class,
                    'options' => array(
                        'messages' => array(
                            NotEmpty::IS_EMPTY => 'Confirme a senha',
                            NotEmpty::INVALID => 'Confirme a senha',
                        ),
                    ),
                ),
                array(
                    'name' => Identical::class,
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

            //define os password com obrigatórios na requisição de create
            if($createRequest) {
                $this->get('password')->setRequired(true);
                $this->get('passwordConfirm')->setRequired(true);
            }

            $this->setData($data);
        }
    }
}