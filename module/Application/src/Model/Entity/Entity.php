<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace Application\Model\Entity;


use Doctrine\ORM\EntityManager;

/**
 * Class Entity
 * Representa o modelo de geração das entidades, possui métodos que todas as entidades da aplicação possuem em comum
 * @abstract
 * @package Application\Model\Entity
 */
abstract class Entity
{
    /**
     * Define todos o atributos de acordo com o valor e a key enviada
     *
     * @param string $key nome do atributo
     * @param string $value valor a ser definido para o atributo
     * @return void
     */
    public function __set($key, $value)
    {
        $method = "set" . ucfirst($key);

        if (method_exists($this, $method)) {
            $this->$method($value);
        }
    }

    /**
     * Define todos os dados da entidade com base em um array de dados
     *
     * @param array $data contém todos os dados a serem definidos para a entidade
     * @return void
     */
    public function setData($data)
    {
        foreach($data as $key => $value) {
            $this->__set($key, $value);
        }
    }

    /**
     * Método abstrato que retorna os dados do objeto em formato de array
     * @abstract
     * @return mixed
     */
    abstract public function toArray();
}