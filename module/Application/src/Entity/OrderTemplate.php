<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace Application\Entity;

/*
$order = new OrderTemplate;

$order->add('id', 'c.id');
$order->add('created', 'c.created');

$order->add(array(
    'nomeFantasia' => 'pj.nomeFantasia',
    'nome' => 'p.nome',
    'id' => 'c.id',
    'data' => 'p.created'
));

$order->setParamsFromRoute($this->params()->fromRoute('sort'));

$qb->select(array('c.id, c.codigo, p.nome, p.ativo, p.created, pf.cpf, pj.cnpj, pj.nomeFantasia'))
    ->from('Pessoa\Entity\Cliente', 'c')
    ->leftJoin('c.pessoa', 'p')
    ->leftJoin('Pessoa\Entity\PessoaFisica', 'pf', Join::WITH, 'pf.id = p.id')
    ->leftJoin('Pessoa\Entity\PessoaJuridica', 'pj', Join::WITH, 'pj.id = p.id')
    ->orderBy($order->getField(), $order->getMode());
*/

class OrderTemplate
{
    private $map = array();

    private $field;

    private $mode;

    public function __construct($alias = null, $field = null)
    {
        if (! is_null($alias)) {
            $this->add($alias, $field);
        }
    }

    public function add($alias, $field = null)
    {
        if (is_string($alias)) {
            $this->map[$alias] = $field;
        } else {
            $this->map = $alias;
        }
    }

    public function setField($field)
    {
        $this->field = $field;
    }

    public function getField()
    {
        return $this->field;
    }

    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    public function getMode()
    {
        return $this->mode;
    }

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
