<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Service;
use Doctrine\ORM\EntityManager;
use SourceCode\Entity\SourceCode;
use SourceCode\Model\CodeBypassCommand;
use SourceCode\Model\Vertex;

/**
 * Class AnalysisStructure
 * Realiza a análise dos códigos fonte
 * @package SourceCode\Service
 */
class AnalysisStructure
{
    protected $entityManager;

    protected $codeCommands;

    protected $vertices;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->vertices = array();
    }

    /**
     * @param SourceCode $sourceCode
     * @throws \Exception
     */
    public function setVertices(SourceCode $sourceCode)
    {
        $language = $sourceCode->getLanguage();
        //todo verificar se deve ser colocado um try catch
        //1. Realiza a extração das estruturas de desvio
        $dataCollectService = new DataCollect($this->entityManager);
        $this->codeCommands = $dataCollectService->getDataFromCode($sourceCode);

        //2. Cria o vértice de início de código
        $startCodeVertex = new Vertex();
        $startCodeVertex->setName($language->getStartNameVertex());
        array_push($this->vertices, $startCodeVertex);

        $bypassController = new CodeBypassCommand();

        /*3. Percorre a Lista de Comandos de Desvio criada pelo service DataCollect */
        foreach ($this->codeCommands as $key => $codeCommand) {
            /* 2.1 Se o comando for abre chaves, será localizado o seu fecha chaves,
               setando o atributo RefAbertura do fecha chaves com a posição
               do comando anterior ao abre chaves */
            if($codeCommand->getName() === "{") {
                $endBlockIndex = $this->findsBlockEnd($key);
                $bypassController = &$this->codeCommands[$endBlockIndex]; //adquire o comando por referência
                $bypassController->setOpeningCommandIndex(($key-1));
            }
            /*2.2 Se o comando for um fecha chaves, cria-se o vértice do tipo END*/
            else if ($codeCommand->getName() === "}") {
                $endVertex = new Vertex();
                //retorna o comando de abertura desse bloco
                $openingVertex = $this->codeCommands[$codeCommand->getOpeningCommandIndex()];
                /* O Nome do Vértice vai ser o nome correspondente ao final da estrutura do código da linguagem
                  + o nome do comando que abre o bloco */
                $endVertex->setName($language->getEndCodeStructure().$openingVertex->getName());
                // Armazena no vértice END o índice do vértice que abre o bloco
                $endVertex->setOpeningVertexIndex($openingVertex->getReferentVertexIndex());
                array_push($this->vertices, $endVertex);
                // Informa qual vértice o comando pertence
                $this->codeCommands[$key]->setReferentVertexIndex((count($this->vertices)-1));
            }
            /* 2.3 Neste momento um comando é transformado em um vértice */
            else if($codeCommand->getName() !== ".") {
                //Cria o vértice
                $commandVertex = new Vertex();
                $commandVertex->setName($codeCommand->getName());
                $commandVertex->setInitialLineNumber($codeCommand->getInitialLineNumber());
                $commandVertex->setEndLineNumber($codeCommand->getEndLineNumber());
                array_push($this->vertices, $commandVertex);

                //todo realizar verificações de if-then, do while e case aqui

            }
        }

    }

    /**
     * Indica se o vértice presente em um determinado índice possui dentro em seu bloco de comandos outros vértices
     * @param $index int
     * @return bool
     */
    private function containsBlockOpening($index)
    {
        /* Percorre a lista de comandos de desvio do código */
        foreach ($this->codeCommands as $key => $codeCommand) {
            /* Encontra qual é o comando referente ao index vértice que está sendo verificado e
               verifica se o próximo comando após esse vértice é de abertura de bloco */
            if($codeCommand->getReferentVertexIndex() == $index && $this->codeCommands[($key+1)]->getName() == "{") {
                return true;
            }
        }
        return false;
    }

    /**
     * Localiza a posição de fechamento de bloco (} criado por um {)
     * @param $blockStartIndex
     * @return int
     */
    private function findsBlockEnd($blockStartIndex)
    {
        $controller = array();
        $result = 0;

        //Percorre a lista de comandos de desvio
        for($i = $blockStartIndex; $i < count($this->codeCommands); $i++) {
            //Adiciona o { a uma pilha
            if($this->codeCommands[$i]->getName() == "{")
                array_push($controller, "{");
            //Quando encontrado um }, remover um { da pilha
            if($this->codeCommands[$i]->getName() == "}")
                array_pop($controller);
            /* Se ao remover um { a pilha ficar vazia, significa que
               naquele momento foi encontrado o } correspondente. */
            if(count($controller) < 1)
                return $i;

        }
        return $result;
    }


}