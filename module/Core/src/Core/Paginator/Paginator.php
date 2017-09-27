<?php
/**
 * XC ERP
 * @copyright Copyright (c) XC Ltda
 * @author Wagner Silveira <wagnerdevel@gmail.com>
 */
namespace Core\Paginator;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;

class Paginator
{
    /**
     * @var int
     */
    private $itemCountPerPage = 30;

    /**
     * @var int
     */
    private $count = null;

    /**
     * @var string
     */
    private $countField = 'id';

    /**
     * @var Doctrine\ORM\QueryBuilder
     */
    private $queryBuilder;
    
    /**
     * @param Doctrine\ORM\QueryBuilder $queryBuilder 
     */
    public function __construct(QueryBuilder $queryBuilder, $itemCountPerPage = null)
    {
        $this->queryBuilder = $queryBuilder;

        if (is_numeric($itemCountPerPage)) {
            $this->itemCountPerPage = $itemCountPerPage;
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        if (is_null($this->count)) {
            $fromAlias = $this->queryBuilder->getRootAliases();
            $fromAlias = 'COUNT(DISTINCT '. $fromAlias[0] .'.'. $this->countField .') as a';

            $query = $this->cloneQuery($this->queryBuilder);

            $query->select($fromAlias)
                ->setFirstResult(null)
                ->setMaxResults(null)
                ->resetDQLPart('groupBy')
                ->resetDQLPart('orderBy')
                ;

            try {
                $this->count = (int) $query->getQuery()->getSingleScalarResult();
            } catch (\Exception $e) {
                die($e->getMessage());
                $this->count = 0;
            }
        }

        return $this->count;
    }

    /**
     * @param integer $page 
     * @return array
     */
    public function getItems($page = 1)
    {
        if (! is_numeric($page)) {
            $page = 1;
        }

        if (is_null($this->queryBuilder->getFirstResult())) {
            $this->queryBuilder->setFirstResult(($page - 1) * $this->itemCountPerPage);
            $this->queryBuilder->setMaxResults($this->itemCountPerPage);
        }

        return $this->queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * Clones a query.
     *
     * @param Query $query The query.
     * @return Query The cloned query.
     */
    private function cloneQuery(QueryBuilder $query)
    {
        $cloneQuery = clone $query;

        return $cloneQuery;
    }

    /**
     * Sets the value of itemCountPerPage.
     *
     * @param int $itemCountPerPage the item count per page
     */
    public function setItemCountPerPage($itemCountPerPage)
    {
        if (is_numeric($itemCountPerPage)) {
            $this->itemCountPerPage = $itemCountPerPage;
        }
    }

    /**
     * Gets the value of itemCountPerPage.
     *
     * @return int
     */
    public function getItemCountPerPage()
    {
        return $this->itemCountPerPage;
    }

    /**
     * Sets the value of countField.
     *
     * @param string $countField the count field
     */
    public function setCountField($countField)
    {
        $this->countField = $countField;
    }

    /**
     * Gets the value of countField.
     *
     * @return string
     */
    public function getCountField()
    {
        return $this->countField;
    }
}
