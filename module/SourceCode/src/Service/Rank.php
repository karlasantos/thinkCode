<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Service;


use Doctrine\ORM\EntityManager;
use SourceCode\Entity\AnalysisResults;

class Rank
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function updateRank($problemId)
    {
        $analysis = $this->entityManager->createQueryBuilder()
                    ->select('analysis.id as analysisId, user.id as userId, problem.id as problemId, analysis.arithmeticMean, sourceCode.id as sourceCodeId')
                    ->from(AnalysisResults::class, 'analysis')
                    ->leftJoin('analysis.sourceCode', 'sourceCode')
                    ->leftJoin('sourceCode.user', 'user')
                    ->leftJoin('analysis.problem', 'problem')
                    ->where('problem.id = :problemId')
                    ->setParameter('problemId',$problemId)
                    ->orderBy('analysis.arithmeticMean', 'ASC');

        //remove toda a estrutura anterior do ranking
        $this->entityManager->createQueryBuilder()->delete(\SourceCode\Entity\Rank::class, 'rank')
                            ->where('rank.problemId = :problemId')
                            ->setParameter('problemId', $problemId)
                            ->getQuery()
                            ->execute();
    }

}