<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Service;


use Doctrine\ORM\EntityManager;
use Exception;
use SourceCode\Model\Entity\Problem;
use SourceCode\Validation\RankValidator;
use \SourceCode\Model\Entity\Rank as RankEntity;
use SourceCode\Model\Entity\SourceCode as SourceCodeEntity;

class Rank
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Atualiza o Ranking do sistema de acordo com a nova submissão
     *
     * @param $data
     * @param SourceCodeEntity $sourceCode
     * @return int|string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws Exception
     */
    public function updateRank($data, SourceCodeEntity $sourceCode)
    {
        $rankFilter = new RankValidator($data);
        $newRank = new RankEntity();
        $newRank->setData($rankFilter->getValues());
        $newRank->setSourceCode($sourceCode);
        $sourceCode->setRanking($newRank);
        $this->entityManager->persist($sourceCode);

        //busca o problema
        $problem = $this->entityManager->find(Problem::class, $rankFilter->getValue('problemId'));

        //define o problema no rank
        if($problem instanceof Problem && $problem->getId() == $sourceCode->getProblem()->getId())
            $newRank->setProblem($problem);
        else
            throw new Exception("Inconsistência de problemas do código fonte e do rank.");

        //monta a query para buscar todos os dados do rank atual
        $qb = $this->entityManager->createQueryBuilder()
            ->select('rank.id, rank.ranking, rank.analysisMean, problem.id as problemId, user.id as userId, sc.id as sourceCodeId')
            ->from(RankEntity::class, 'rank')
            ->leftJoin('rank.problem', 'problem')
            ->leftJoin('rank.sourceCode', 'sc')
            ->leftJoin('sc.user', 'user')
            ->where('rank.problem = :problemId')
            ->setParameter('problemId',  $rankFilter->getValue('problemId'));

        //busca o ranking atual
        $ranksSaved = $qb->orderBy('rank.ranking', 'ASC')
                        ->getQuery()
                        ->getArrayResult();

        //verifica se existe ranking salvo
        if(count($ranksSaved) > 0) {
            //adiciona automaticamente o novo rank ao último lugar
            end($ranksSaved);
            //busca a posição do último colocado e adiciona mais um na posição
            $newRanking = $ranksSaved[key($ranksSaved)]['ranking']+1;

            //define e salva o usuário na última posição
            $newRank->setRanking($newRanking);
            $this->entityManager->persist($newRank);
            $this->entityManager->flush();

            $ranksSaved = $qb->orderBy('rank.analysisMean', 'ASC')
                            ->getQuery()
                            ->getArrayResult();

            foreach ($ranksSaved as $key => $rankSaved) {
                $rank = $this->entityManager->find(RankEntity::class, $rankSaved['id']);
                if($rank instanceof RankEntity) {
                    //define a nova posição do rank de acordo com a média de analises
                    $rank->setRanking($key+1);
                    $this->entityManager->persist($rank);
                }

                if($rankSaved['userId'] == $rank->getSourceCode()->getUser()->getId()) {
                    $ranking = $key+1;
                }
            }

            $this->entityManager->flush();
        } else {
            $ranking = 1;
            $newRank->setRanking($ranking);
            $this->entityManager->persist($newRank);
            $this->entityManager->flush();
        }

        return $ranking;
    }
}