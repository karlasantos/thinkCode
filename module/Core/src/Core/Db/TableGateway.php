<?php
namespace Core\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;
use Core\Model\EntityException;

class TableGateway extends AbstractTableGateway
{
    /**
     * Primary Key field name
     *
     * @var string
     */
    protected $primaryKeyField;

    /**
     * ObjectPrototype
     * @var stdClass
     */
    protected $objectPrototype;

    public function __construct(Adapter $adapter, $table, $objectPrototype)
    {
        $this->adapter = $adapter;
        $this->table = $objectPrototype->getTableName();
        $this->objectPrototype = $objectPrototype;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype($objectPrototype);
    }

    public function initialize()
    {
        parent::initialize();

        $this->primaryKeyField = $this->objectPrototype->getPrimaryKeyField();
        
        if ( ! is_string($this->primaryKeyField)) {
            $this->primaryKeyField = 'id';
        }
    }   
    
    public function fetchAll($columns = null, $where = null, $limit = null, $offset = null)
    {
        $select = new Select();
        $select->from($this->getTable());

        if ($columns)
            $select->columns($columns);

        if ($where)
            $select->where($where);

        if ($limit)
            $select->limit((int) $limit);

        if ($offset)
            $select->offset((int) $offset);

        return $this->selectWith($select);
    }

    public function get($id)
    {
        $id     = (int) $id;
        $rowset = $this->select(array($this->primaryKeyField => $id));
        $row    = $rowset->current();

        if (! $row) {
            throw new EntityException("Could not find row $id");
        }

        return $row;
    }

    public function save($object)
    {
        $data = $object->getData();

        $id = (int) isset($data[$this->primaryKeyField]) ? $data[$this->primaryKeyField] : 0;

        if ($id == 0) {
            if (method_exists($object, 'getCreated') && is_null($object->getCreated())) {
                $object->setCreated(date('Y-m-d H:i:s'));

                $data['_created'] = $object->getCreated();
            }

            if ($this->insert($data) < 1) {
                throw new EntityException("Inserr Error", 1);
            }

            $object->setId($this->lastInsertValue);
        } else {
            if (! $this->get($id)) {
                throw new EntityException('Id does not exist');
            }

            if (method_exists($object, 'getUpdated')) {
                $object->setUpdated(date('Y-m-d H:i:s'));

                $data['_updated'] = $object->getUpdated();
            }

            if ($this->update($data, array($this->primaryKeyField => $id)) < 1) {
                throw new EntityException("Error on update", 1);
            }
        }

        return $object;
    }

    public function delete($id)
    {
        return parent::delete(array($this->primaryKeyField => $id));
    }
}
