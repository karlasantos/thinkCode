<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Service;
use Doctrine\ORM\EntityManager;
use SourceCode\Entity\Language;
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

    protected $dataCollectService;

    protected $languageEntity;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->vertices = array();
        $this->dataCollectService = new DataCollect($this->entityManager);
        $this->languageEntity = null;
    }

    /**
     * @param SourceCode $sourceCode
     * @return array
     * @throws \Exception
     */
    public function setVertices(SourceCode $sourceCode)
    {
        $this->languageEntity = $sourceCode->getLanguage();
        //todo verificar se deve ser colocado um try catch
        //1. Realiza a extração das estruturas de desvio
        $this->codeCommands = $this->dataCollectService->getDataFromCode($sourceCode);

        //salva o language service para não utilizar sempre o método get
        $languageService = $this->dataCollectService->getLanguageService();

        //2. Cria o vértice de início de código
        $initialVertex = new Vertex();
        $initialVertex->setName($this->languageEntity->getInitialVertexName());
        array_push($this->vertices, $initialVertex);

        $bypassController = new CodeBypassCommand();

        /*3. Percorre a Lista de Comandos de Desvio criada pelo service DataCollect */
        foreach ($this->codeCommands as $key => $codeCommand) {
            if($codeCommand instanceof CodeBypassCommand) {
                /* 2.1 Se o comando for abre chaves, será localizado o seu fecha chaves,
                   setando o atributo RefAbertura do fecha chaves com a posição
                   do comando anterior ao abre chaves */
                if ($codeCommand->getName() === "{") {
                    $startBlockIndex = $this->findsBlockEnd($key);
                    $bypassController = &$this->codeCommands[$startBlockIndex]; //adquire o comando por referência
                    $bypassController->setOpeningCommandIndex(($key - 1));
                }
                /*2.2 Se o comando for um fecha chaves, cria-se o vértice do tipo END*/
                else if ($codeCommand->getName() === "}") {
                    $endBypassCommandVertex = new Vertex();
                    //retorna o comando de abertura desse bloco
                    $openingVertex = $this->codeCommands[$codeCommand->getOpeningCommandIndex()];
                    /* O Nome do Vértice vai ser o nome correspondente ao final da estrutura do código da linguagem
                      + o nome do comando que abre o bloco */
                    $endBypassCommandVertex->setName($this->languageEntity->getEndVertexName() . $openingVertex->getName());
                    // Armazena no vértice END o índice do vértice que abre o bloco
                    $endBypassCommandVertex->setOpeningVertexIndex($openingVertex->getReferentVertexIndex());
                    array_push($this->vertices, $endBypassCommandVertex);
                    end($this->vertices);
                    // Informa qual vértice o comando pertence
                    $this->codeCommands[$key]->setReferentVertexIndex(key($this->vertices));
                } /* 2.3 Neste momento um comando é transformado em um vértice */
                else if ($codeCommand->getName() !== ".") {
                    //Cria o vértice
                    $commandVertex = new Vertex();
                    $commandVertex->setName($codeCommand->getName());
                    $commandVertex->setInitialLineNumber($codeCommand->getInitialLineNumber());
                    $commandVertex->setEndLineNumber($codeCommand->getEndLineNumber());
                    array_push($this->vertices, $commandVertex);
                    end($this->vertices);
                    $this->codeCommands[$key]->setReferentVertexIndex(key($this->vertices));

                    //Cria o vértice do tipo END para os comandos que não possuem abertura e fechamento de bloco
                    if ($this->codeCommands[$key + 1] instanceof CodeBypassCommand &&
                        $this->codeCommands[$key + 1]->getName() !== "{" &&
                        !$languageService->isInitialBypassCommandElse($codeCommand->getName())//) {
                        && !$languageService->isInitialBypassCommandElseIf($codeCommand->getName())) {

                        end($this->vertices);
                        $endKeyVertex = key($this->vertices);

                        //todo comentado para trabalhos futuros
//                        //se o comando for IF ou ELSE IF cria o vértice THEN
//                        if ($languageService->isInitialBypassCommandIf($codeCommand->getName()) || $languageService->isInitialBypassCommandElseIf($codeCommand->getName())) {
//                            $thenVertex = new Vertex();
//                            $thenVertex->setName($this->languageEntity->getIfThenNameVertex());
//                            $thenVertex->setOpeningVertexIndex($endKeyVertex);
//                            array_push($this->vertices, $thenVertex);
//                        }

                        /* Neste momento é criado um vértice do Tipo END para a seguinte situação:
                            - Se o comando for IF ou FOR e não abrir bloco*/
                        $endBypassCommandVertex = new Vertex();
                        /* O Nome do Vértice vai ser o nome correspondente ao final da estrutura do código da linguagem
                            + o nome do comando que abre o bloco */
                        $endBypassCommandVertex->setName($this->languageEntity->getEndVertexName() . $codeCommand->getName());
                        $endBypassCommandVertex->setOpeningVertexIndex($endKeyVertex);
                        array_push($this->vertices, $endBypassCommandVertex);
                    }
                }
            }
        }

        /*3 Cria o vértice de ENDCODE */
        $endVertex = new Vertex();
        $endVertex->setName($this->languageEntity->getEndVertexName());
        array_push($this->vertices, $endVertex);

        return $this->vertices;
    }

    public function setEdges(Language $language)
    {
        $languageService = $this->dataCollectService->getLanguageService();
        //armazenam as posições dos vértices de destino na lista de vértices
        $right = -1;
        $left = -1;

        foreach ($this->vertices as $key => $vertex) {
            $vertexName = $this->removeEndVertexPrefix($vertex->getName());
            /*1. Os vértices de INÍCIO DE ESTRUTURA, IF, FOR e seus vértices de FIM sempre vão se ligar
                ao próximo vértice pela sua esquerda*/
            if($vertexName === $language->getInitialVertexName()                ||
               $languageService->isInitialBypassCommandIf($vertex->getName()) ||
               $languageService->isInitialBypassCommandFor($vertexName)       ||
               $languageService->isInitialBypassCommandWhile($vertexName)
            ) {
                $left = $key + 1;
            }

            /* 2. O Vértice IF vai se ligar pela sua direita ao vértice que localiza-se
                  após o seu ENDIF correspondente. Ex.: IF ENDIF ELSE, IF = Esq. -> ENDIF | Dir. -> ELSE*/
            if ($languageService->isInitialBypassCommandIf($vertex->getName()))
            {
                /* 2.1 Através do atributo OpeningVertexIndex localizado no ENDIF é possível localizar
                     onde termina o bloco criado pelo IF*/
                foreach($this->vertices as $key2 => $vertex2) {
                    //todo colocar para caso de implementar then
                    //if($vertex2->getName() !== $this->languageEntity->getIfThenNameVertex() && $vertex2->getOpeningVertexIndex() === $key) {
                    if($vertex2->getOpeningVertexIndex() === $key) {
                        $right = $key2 + 1;
                        break;
                    }
                }
            }
            /* 3. Os vértices ELSEIF e ELSE ligam-se pela esquerda ao ENDIF quando não abrem bloco
               e ligam-se pela direita ao próximo vértice se houver.*/
            else if($languageService->isInitialBypassCommandElse($vertex->getName()) || $languageService->isInitialBypassCommandElseIf($vertex->getName())) {
                /* 3.1 Se o ELSEIF ou ELSE abrirem bloco, ligam-se pela esquerda ao próximo vértice.*/
                if($this->containsBlockOpening($key)) {
                    $left = $key + 1;

                    /* 3.1.1 Este trecho trata a situação em que o ELSE IF não tem um ELSE logo após*/
                    if($languageService->isInitialBypassCommandElseIf($vertex->getName())) {
                        foreach($this->vertices as $key2 => $vertex2) {
                            if($vertex2->getOpeningVertexIndex() === $key) {
                                if(!$this->vertices[$key2 + 1]->getName() === $this->languageEntity->getEndVertexName())
                                    $right = $key2 + 1;
                                break;
                            }
                        }
                    }
                }
                /* 3.2 Se o ELSEIF ou ELSE não abrirem bloco, ligam-se pela esquerda ao ENDIF e pela direita
                    ao próximo vértice.*/
                else {
                    /* 3.2.1 Este trecho trata a situação em que o ELSE IF não tem logo após o ELSE*/
                    if ($languageService->isInitialBypassCommandElseIf($vertex->getName())) {
                        if (!$this->vertices[$key + 1]->getName() === $this->languageEntity->getEndVertexName())
                            $right = $key + 1;
                        break;
                    }
                    //todo PAREI AQUI
                    /* 3.2.2 Este trecho procura o ENDIF no qual o ELSE ou ELSEIF deve se ligar pela esquerda
                       Percorre até o inicio da lista de vertices */
                    for ($i = $key - 1; $i >= 0; $i--) {
                        /* Se encontrar um ENDELSEIF, pular para o seu vertice de abertura*/

                    }
                }
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
     * Localiza a posição de fechamento de bloco (}) criado por uma abertura de bloco ({)
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
            if($this->codeCommands[$i] instanceof CodeBypassCommand) {
                if ($this->codeCommands[$i]->getName() == "{")
                    array_push($controller, "{");
                //Quando encontrado um }, remover um { da pilha
                if ($this->codeCommands[$i]->getName() == "}")
                    array_pop($controller);
            }
            /* Se ao remover um { a pilha ficar vazia, significa que
               naquele momento foi encontrado o } correspondente. */
            if(count($controller) < 1)
                return $i;

        }
        return $result;
    }

    private function removeEndVertexPrefix($vertexName)
    {
        return str_replace($this->languageEntity->getEndVertexName(), "", $vertexName);
    }
}