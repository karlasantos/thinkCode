<?php
namespace Core\Paginator;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\Query\Expr\Andx;

/*
$search = new SearchTemplate($qb);

$search->setParams($this->getRequest()->getQuery('search'));
$search->addField('id', 'c.id', 'eq');
$search->addField('nome', 'LOWER(p.nome)', 'like', function ($query) {
    return strtolower('%'. str_replace(' ', '%', $query) .'%');
});
 */
class SearchTemplate
{
    /**
     * @var array
     */
    private $params;

    /**
     * @var array
     */
    private $options;

    private $instanceofLastOption = 0;

    const PREPARE_STRING = 'callPrepareString';
    const PREPARE_STRING_SPECIAL_CHARS = 'callPrepareSpecialChars';

    /**
     * @param Doctrine\ORM\QueryBuilder $queryBuilder
     */
    public function __construct()
    {
        $this->options = array();
    }

    /**
     * <code>
     * array(
     *     'alias' => 'value for search',
     *     'name' => 'Oregon'
     * );
     * </code>
     *
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * Adiciona um mapper para converter os alias em campos da propria tabela.
     *
     * <code>
     * $search->addField('id', 'c.id', 'eq');
     * $search->addField('nome', 'LOWER(p.nome)', 'like', function ($query) {
     *     return strtolower('%'. str_replace(' ', '%', $query) .'%');
     * });
     * </code>
     *
     * @param string $alias Apelido para o campo $fieldWhere. Normalmente um parametro http. Ex.: name.
     * @param string $fieldWhere nome do campo da tabela correspondente ao alias. Ex.: LOWER(person.name).
     * @param string $exprMethod um metodo de Doctrine\ORM\Query\Expr.
     * @param type $callPrepare uma funcao para preparar o parametro da pesquisa.
     */
    public function addField($alias, $fieldWhere = null, $exprMethod = null, $callPrepare = null)
    {
        $this->options[$alias] = $this->makeField($fieldWhere, $exprMethod, $callPrepare);
    }

    public function makeField($fieldWhere, $exprMethod, $callPrepare = null)
    {
        $options = array(
            'field'   => $fieldWhere,
            'method'  => $exprMethod,
            'prepare' => $callPrepare,
        );

        return $options;
    }

    public function add($alias, $expr)
    {
        $this->options[$alias] = $expr;
    }

    /**
     * Txt
     *
     * [php]
     *     array(
     *         'id' => array(
     *             'field'  => 'c.id',
     *             'method' => 'eq'
     *         ),
     *         'codigo' => array(
     *             'field'  => 'c.codigo',
     *             'method' => 'eq'
     *         ),
     *     );
     *
     * @param array $group
     */
    public function addGroup(array $group)
    {
        $this->options[] = $group;
    }

    public function make(QueryBuilder $qb)
    {
        $andx = new Andx;
        $expr = new Expr;

        foreach ($this->options as $op => $option) {
            if (! is_string($op)) {
                $orx = new Orx;

                foreach ($option as $searchKey => $field) {
                    if (isset($this->params[$searchKey])) {
                        $orx->add($expr->$field['method']($field['field'], ':'. $searchKey));

                        if (is_callable($field['prepare'])) {
                            $this->params[$searchKey] = $field['prepare']($this->params[$searchKey]);
                        } elseif (is_string($field['prepare']) && method_exists($this, $field['prepare'])) {
                            $this->params[$searchKey] = $this->$field['prepare']($this->params[$searchKey]);
                        }

                        $qb->setParameter($searchKey, $this->params[$searchKey]);
                    }
                }

                $qb->andWhere($orx);
            } else {
                $searchKey = $op;
                $field     = $option;

                if (isset($this->params[$searchKey])) {
                    if (is_string($field)) {
                        $qb->andWhere($field);
                    } else {
                        $qb->andWhere($expr->$field['method']($field['field'], ':'. $searchKey));

                        if (isset($field['prepare'])) {
                            //$this->params[$searchKey] = $field['prepare']($this->params[$searchKey]);
                            if (is_callable($field['prepare'])) {
                                $this->params[$searchKey] = $field['prepare']($this->params[$searchKey]);
                            } elseif (is_string($field['prepare']) && method_exists($this, $field['prepare'])) {
                                $this->params[$searchKey] = $this->$field['prepare']($this->params[$searchKey]);
                            }
                        }

                        $qb->setParameter($searchKey, $this->params[$searchKey]);
                    }
                }
            }
        }

        // $qb->andWhere($andx);
    }

    private function callPrepareString($query)
    {
        return ('%'. strtolower(str_replace(' ', '%', $query)) .'%');
    }

    public static function callPrepareSpecialChars($query)
    {
        return ('%'. strtolower(str_replace(' ', '%', self::removeAcentos($query))) .'%');
    }

    /**
     *
     * @param string $str
     * @return string
     */
    public static function removeAcentos($str)
    {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', 'Ά', 'ά', 'Έ', 'έ', 'Ό', 'ό', 'Ώ', 'ώ', 'Ί', 'ί', 'ϊ', 'ΐ', 'Ύ', 'ύ', 'ϋ', 'ΰ', 'Ή', 'ή');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', 'Α', 'α', 'Ε', 'ε', 'Ο', 'ο', 'Ω', 'ω', 'Ι', 'ι', 'ι', 'ι', 'Υ', 'υ', 'υ', 'υ', 'Η', 'η');

        return str_replace($a, $b, $str);
    }
}