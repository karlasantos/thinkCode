<?php
/**
 * Created by PhpStorm.
 * User: karla
 * Date: 19/05/18
 * Time: 21:36
 */

namespace SourceCode\Service;


use Doctrine\ORM\EntityManager;
use SourceCode\Entity\SourceCode as SourceCodeEntity;

class SourceCode
{
    /**
     * Gerenciador de entidades do Doctrine
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param $userId
     * @return array
     */
    public function getUserSourceCodesSimple($userId)
    {
        $sourceCodes = $this->entityManager->createQueryBuilder()
                        ->select('sc.id')
                        ->from(SourceCodeEntity::class, 'sc')
                        ->where('sc.user = :userId')
                        ->setParameter('userId', $userId)
                        ->getQuery()
                        ->getArrayResult();

        return array_column($sourceCodes, 'id');
    }

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

}