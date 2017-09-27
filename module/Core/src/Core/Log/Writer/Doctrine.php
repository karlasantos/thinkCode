<?php
/**
 * XC ERP
 * @copyright Copyright (c) XC Ltda
 * @author Wagner Silveira <wagnerdevel@gmail.com>
 */
namespace Core\Log\Writer;

use Traversable;
use Zend\Log\Writer\AbstractWriter;
use Zend\Db\Adapter\Adapter;
use Zend\Log\Exception;
use Zend\Log\Formatter\Db as DbFormatter;

class Doctrine extends AbstractWriter
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var Core\Model\Entity
     */
    protected $entity;

    /**
     * @param Doctrine\ORM\EntityManager $em
     * @param Core\Model\Entity          $entity
     */
    public function __construct(\Doctrine\ORM\EntityManager $em, $entity)
    {
        $this->em     = $em;
        $this->entity = $entity;
    }

    /**
     * Write a message to the log.
     *
     * @param array $event event data
     * @return void
     * @throws Exception\RuntimeException
     */
    protected function doWrite(array $event)
    {
        if (null === $this->em) {
            throw new Exception\RuntimeException('EntityManager is null');
        }

        $this->entity->setData($event);

        $this->em->persist($this->entity);
        $this->em->flush();
    }
}
