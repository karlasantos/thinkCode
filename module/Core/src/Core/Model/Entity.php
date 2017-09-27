<?php
namespace Core\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\Exception\InvalidArgumentException;

abstract class Entity implements InputFilterAwareInterface
{
    /**
     * Filters
     * 
     * @var InputFilter
     */
    protected $inputFilter = null;

    /**
     * Erros ocasionados ao persistir uma entidade
     * 
     * @var array
     */
    protected $inputErrors = array();

    /**
     * Set and validate field values
     *
     * @param string $key nome da coluna da tabela
     * @param string $value
     * @return void
     */
    public function __set($key, $value) 
    {
        // $keys   = explode('_', $key);
        $method = "set" . ucfirst($key);

        // foreach ($keys as $word) {
        //     $method .= ucfirst($word);
        // }
        
        if (method_exists($this, $method)) {
            $this->$method($value);
        }
        //  else {
        //     throw new EntityException("Nenhum metodo set(...) correspondente ao atributo {{$key}} de Entity::$key.");
        // }
    }

    /**
     * @param string $key
     * @return mixed 
     */
    public function __get($key) 
    {
        if (isset($this->tableMap[$key])) {
            $attrName = $this->tableMap[$key];

            $firstChar = strtoupper(substr($attrName, 0, 1));
            $method    = "get" . $firstChar . substr($attrName, 1);

            if (method_exists($this, $method)) {
                return $this->$method();
            } else {
                throw new EntityException("Nenhum metodo get(...) correspondente ao atributo {{$key}} de Entity::\$tableMap.");
            }

            return $this->$method();
        }
    }

    /**
     * Set all entity data based in an array with data
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
     * Return all entity data in array format
     *
     * @return array
     */
    public function getData($em)
    {
        $className = get_class($this);
        $metaData = $em->getClassMetadata($className);

        $data = get_object_vars($this);

        return $metaData;
    }

    /**
     * @param InputFilterInterface $inputFilter
     * @return void
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new EntityException("Not used");
    }

    /**
     * Entity filters
     *
     * @return InputFilter
     */
    public function getInputFilter() {}


    /**
     * Filter and validate data
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    protected function valid($key, $value)
    {
        if (! $this->getInputFilter())
            return $value;

        try {
            $filter = $this->getInputFilter()->get($key);
        } catch(InvalidArgumentException $e) {
            //não existe filtro para esse campo
            return $value;
        }

        $filter->setValue($value);

        // se eh obrigatorio ou for informado e for invalido
        if (($filter->isRequired() || ! empty($value)) && ! $filter->isValid()) {
            if (is_object($value)) {
                $value = get_class($value);
            }

            throw new EntityException("Invalid input: $key = $value");
        }

        return $filter->getValue($key);
    }
    
    public function getInputErrors()
    {
        return $this->inputErrors;
    }

    public function hasInputErrors()
    {
        return (! empty($this->inputErrors));
    }

    /**
     * Used by TableGateway
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getData();
    }
}
