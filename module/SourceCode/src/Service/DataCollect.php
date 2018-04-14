<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Service;

use Doctrine\ORM\EntityManager;
use SourceCode\Entity\BypassCommand;
use SourceCode\Entity\DataType;
use SourceCode\Entity\LogicalConnective;
use SourceCode\Entity\SourceCode;

/**
 * Class DataCollect
 * Realiza a coleta dos dados necessários para a análise
 * @package SourceCode\Service
 */
class DataCollect
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getDataFromCode(SourceCode $sourceCode)
    {
        $language = $sourceCode->getLanguage();

        //monta a query para trazer todos os comandos de desvio da linguagem utilizada no código fonte
        $diversionCommands = $this->entityManager->createQueryBuilder()
            ->select('bc.id, bc.initialCommandName, bc.terminalCommandName, bc.type')
            ->from(BypassCommand::class, 'bc')
            ->innerJoin('bc.languages', 'language')
            ->where('language.id = :languageId')
            ->setParameter('languageId', $language->getId());

        //retorna os comandos condicionais da linguagem utilizada no código fonte
        $conditionalCommands = $diversionCommands->andWhere('bc.type like conditional')
            ->getQuery()
            ->getArrayResult();

        //retorna os comandos de repetição da linguagem utilizada no código fonte
        $loopCommands = $diversionCommands->andWhere('bc.type like loop')
            ->getQuery()
            ->getArrayResult();

        //retorna os conectivos lógicos da linguagem utilizada no código fonte
        $logicalConnectives = $this->entityManager->createQueryBuilder()
            ->select('lc.id, lc.name')
            ->from(LogicalConnective::class, 'lc')
            ->innerJoin('lc.languages', 'language')
            ->where('language = :languageId')
            ->setParameter('languageId', $language->getId())
            ->getQuery()
            ->getArrayResult();
        $logicalConnectives = array_column($logicalConnectives, 'name');

        //retorna os tipos de dados da linguagem utilizada no código fonte
        $dataTypes = $this->entityManager->createQueryBuilder()
            ->select('dt.id, dt.name')
            ->from(DataType::class, 'dt')
            ->innerJoin('dt.languages', 'language')
            ->where('language = :languageId')
            ->setParameter('languageId', $language->getId())
            ->getQuery()
            ->getArrayResult();
        $dataTypes = array_column($dataTypes, 'name');


    }

}