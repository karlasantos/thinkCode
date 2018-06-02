<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace Application\Model\Entity;

/**
 * Class OrderTemplate
 * Ordenador de registros.
 * @package Application\Model\Entity
 * @example order = new OrderTemplate;

    $order->add('id', 'rank.id');

    $order->setParamsFromRoute($this->params()->fromRoute('sort'));
    $qb->select('rank')
        ->from('SourceCode\Entity\Rank', 'rank')->orderBy($order->getField(), $order->getMode());
 *
 */
class OrderTemplate
{
    /**
     * Array de mapeamento de aliases e valores de ordenação
     *
     * @var array
     */
    private $map = array();

    /**
     * Atributo a ser ordenado
     *
     * @var string
     */
    private $field;

    /**
     * Mode de ordenação:
     * - ASC: crescente
     * - DESC: decrescente
     *
     * @var string
     */
    private $mode;

    /**
     * Construtor da classe
     *
     * OrderTemplate constructor.
     * @param string $alias alias do atributo no array de ordenação
     * @param string $field nome do atributo a ser ordenado
     */
    public function __construct($alias = null, $field = null)
    {
        if (! is_null($alias)) {
            $this->add($alias, $field);
        }
    }

    /**
     * Método de adição de ordenação
     *
     * @param array|string $alias alias do atributo no array de ordenação
     * @param string|null $field nome do atributo a ser ordenado
     */
    public function add($alias, $field = null)
    {
        if (is_string($alias)) {
            $this->map[$alias] = $field;
        } else {
            $this->map = $alias;
        }
    }

    /**
     * Define o atributo a ser ordenado
     *
     * @param $field string
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    /**
     * Retorna o atributo a ser ordenado
     *
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Define o modo de ordenação (ASC ou DESC)
     *
     * @param $mode string
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * Retorna o modo de ordenação (ASC ou DESC)
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Define os atributos de ordenação através de parâmetros retornados na requisição
     *
     * @param $params array
     */
    public function setParamsFromRoute($params)
    {
        if (empty($params) || strpos($params, ':') === null) {
            $params = 'id:asc';
        }

        $sort = explode(':', $params);

        if (! in_array($sort[0], array_keys($this->map))) {
            $this->field = count($this->map) > 0 ? current($this->map) : 'id';
        } else {
            $this->field = $this->map[$sort[0]];
        }

        if (! isset($sort[1]) || (strtolower($sort[1]) != 'asc' && strtolower($sort[1]) != 'desc')) {
            $this->mode = 'ASC';
        } else {
            $this->mode = strtoupper($sort[1]);
        }
    }
}
