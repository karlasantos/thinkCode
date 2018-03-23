<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace Application\Entity;


use Doctrine\ORM\EntityManager;

/**
 * Class Entity
 * @abstract
 * @package Application\Entity
 */
abstract class Entity
{
    /**
     * Define todos o atributos de acordo com o valor e a key enviada
     *
     * @param string $key nome da coluna da tabela
     * @param string $value
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
     * @param array $data
     * @return void
     */
    public function setData($data)
    {
        foreach($data as $key => $value) {
            $this->__set($key, $value);
        }
    }


    /**
     * Retorna todos os dados da entidade em formato de array
     *
     * @param $em EntityManager
     * @return mixed
     */
    public function getData(EntityManager $em)
    {
        $className = get_class($this);
        $metaData = $em->getClassMetadata($className);

        $data = get_object_vars($this);

        return $metaData;
    }

    /**
     * Método abstrato que retorna os dados do objeto em formato de array
     * @abstract
     * @return mixed
     */
    abstract public function toArray();
}