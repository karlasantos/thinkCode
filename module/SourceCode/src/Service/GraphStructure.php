<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Service;
use Doctrine\ORM\EntityManager;
use SourceCode\Model\Entity\Language;
use SourceCode\Model\Entity\SourceCode;
use SourceCode\Model\CodeBypassCommand;
use SourceCode\Model\Vertex;
use SourceCode\Service\Language as LanguageService;

/**
 * Class GraphStructure
 * Gera os dados necessários para montar o Grafo de Fluxo
 * @package SourceCode\Service
 */
class GraphStructure
{
    /**
     * Gerenciador de entidades
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Comandos de desvio do código fonte
     *
     * @var array
     */
    protected $codeCommands;

    /**
     * Vértices do grafo
     * @var array
     */
    protected $vertices;

    /**
     * Serviço de coleta de informações
     * @var DataCollect
     */
    protected $dataCollectService;

    /**
     * GraphStructure constructor.
     * @param EntityManager $entityManager
     * @param DataCollect $dataCollectService
     */
    public function __construct(EntityManager $entityManager, DataCollect $dataCollectService)
    {
        $this->entityManager = $entityManager;
        $this->vertices = array();
        $this->dataCollectService = $dataCollectService;
    }

    /**
     * Define todos os dados do grafo (vértices, arestas e coordenadas de vértices)
     *
     * @param SourceCode $sourceCode
     * @return array
     * @throws \Exception
     */
    public function setGraphData(SourceCode $sourceCode)
    {
        $language = $sourceCode->getLanguage();

        //1. Adquire as estruturas de desvio definidas no DataCollect
        $this->codeCommands = $this->dataCollectService->getCodeCommands();

        //define os vértices
        $this->setVertices($language);

        //define as arestas
        $this->setEdges($language);

        //define as coordenadas
        $this->setCoordinates($language);

        //retorna os vértices com todos os dados
        return $this->vertices;
    }

    /**
     * Gera os vértices do grafo de fluxo
     *
     * @param Language $language
     * @throws \Exception
     */
    private function setVertices(Language $language)
    {
        //salva o language service para não utilizar sempre o método get
        $languageService = $this->dataCollectService->getLanguageService();

        //2. Cria o vértice de início de código
        $initialVertex = new Vertex();
        $initialVertex->setName($language->getInitialVertexName());
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
                    //retorna o comando de abertura desse bloco
                    $openingVertex = $this->codeCommands[$codeCommand->getOpeningCommandIndex()];
                    $endBypassCommandVertex = new Vertex();
                    /* O Nome do Vértice vai ser o nome correspondente ao final da estrutura do código da linguagem
                      + o nome do comando que abre o bloco */
                    $endBypassCommandVertex->setName($language->getEndVertexName() . $openingVertex->getName());
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
                        && !$languageService->isInitialBypassCommandElseIf($codeCommand->getName())
                        && !$languageService->isInitialBypassCommandCaseOrDefault($codeCommand->getName())) {

                        end($this->vertices);
                        $endKeyVertex = key($this->vertices);

                        //todo comentado para trabalhos futuros
//                        //se o comando for IF ou ELSE IF cria o vértice THEN
//                        if ($languageService->isInitialBypassCommandIf($codeCommand->getName()) || $languageService->isInitialBypassCommandElseIf($codeCommand->getName())) {
//                            $thenVertex = new Vertex();
//                            $thenVertex->setName($language->getIfThenNameVertex());
//                            $thenVertex->setOpeningVertexIndex($endKeyVertex);
//                            array_push($this->vertices, $thenVertex);
//                        }

                        /* Neste momento é criado um vértice do Tipo END para a seguinte situação:
                            - Se o comando for IF ou FOR e não abrir bloco*/
                        $endBypassCommandVertex = new Vertex();
                        /* O Nome do Vértice vai ser o nome correspondente ao final da estrutura do código da linguagem
                            + o nome do comando que abre o bloco */
                        $endBypassCommandVertex->setName($language->getEndVertexName() . $codeCommand->getName());
                        $endBypassCommandVertex->setOpeningVertexIndex($endKeyVertex);
                        array_push($this->vertices, $endBypassCommandVertex);
                    }
                }
            }
        }

        /*3 Cria o vértice de ENDCODE */
        $endVertex = new Vertex();
        $endVertex->setName($language->getEndVertexName());
        array_push($this->vertices, $endVertex);
    }

    /**
     * Gera as arestas e armazena essas ligações entre vértices na listagem de vértices
     *
     * @param Language $language
     */
    private function setEdges(Language $language)
    {
        $languageService = $this->dataCollectService->getLanguageService();
        //armazenam as posições dos vértices de destino na lista de vértices
        $right = -1;
        $left = -1;
        $endSwitchIndex = -1;
        $commandsCaseDefaultSwitchIndexes = array();

        foreach ($this->vertices as $key => $vertex) {
            /*1. Os vértices de INÍCIO DE ESTRUTURA, IF, FOR e seus vértices de FIM sempre vão se ligar
                ao próximo vértice pela sua esquerda*/
            if($vertex->getName() === $language->getInitialVertexName()       ||
                $languageService->isInitialBypassCommandIf($vertex->getName()) ||
                $languageService->isInitialBypassCommandFor($vertex->getName())||
                $languageService->isInitialBypassCommandWhile($vertex->getName()) ||
                $languageService->isInitialBypassCommandDoWhile($this->removeEndVertexPrefix($vertex->getName(), $language)) ||
                $vertex->getName() === $language->getEndVertexName().$languageService->getBypassCommandSwitch()['initialCommandName']
            ) {
                $left = $key + 1;
            }

            /* 2. O Vértice IF vai se ligar pela sua direita ao vértice que localiza-se
                  após o seu ENDIF se esse vértice for um ELSE ou ELSEIF.
            Ex.: IF ENDIF ELSE, IF = Esq. -> ENDIF | Dir. -> ELSE*/
            if ($languageService->isInitialBypassCommandIf($vertex->getName()))
            {
                /* 2.1 Através do atributo OpeningVertexIndex localizado no ENDIF é possível localizar
                     onde termina o bloco criado pelo IF*/
                foreach($this->vertices as $key2 => $vertex2) {
                    //todo colocar para caso de implementar then
                    //if($vertex2->getName() !== $language->getIfThenNameVertex() && $vertex2->getOpeningVertexIndex() === $key) {
                    if($vertex2->getOpeningVertexIndex() === $key) {
//                        if($this->vertices[$key2+1] instanceof Vertex && (
//                            $languageService->isInitialBypassCommandElseIf($this->vertices[$key2+1]->getName()) ||
//                            $languageService->isInitialBypassCommandElse($this->vertices[$key2+1]->getName())))
                        $right = $key2 + 1;
                        break;
                    }
                }
            }
            /* 3. Os vértices ELSEIF e ELSE ligam-se pela esquerda ao ENDIF quando não abrem bloco
               e ligam-se pela direita ao próximo vértice se houver.*/
            else if($languageService->isInitialBypassCommandElse($vertex->getName()) ||
                $languageService->isInitialBypassCommandElseIf($vertex->getName())) {
                /* 3.1 Se o ELSEIF ou ELSE abrirem bloco, ligam-se pela esquerda ao próximo vértice.*/
                if($this->containsBlockOpening($key)) {
                    $left = $key + 1;

                    /* 3.1.1 Este trecho trata a situação em que o ELSEIF tem um ELSE ou ELSEIF logo após*/
                    if($languageService->isInitialBypassCommandElseIf($vertex->getName())) {
                        foreach($this->vertices as $key2 => $vertex2) {
                            if($vertex2->getOpeningVertexIndex() === $key) {
                                if(($languageService->isInitialBypassCommandElse($this->vertices[$key2 + 1]->getName()) || $languageService->isInitialBypassCommandElseIf($this->vertices[$key2 + 1]->getName())))
                                    $right = $key2 + 1;
                                break;
                            }
                        }
                    }
                }
                /* 3.2 Se o ELSEIF ou ELSE não abrirem bloco, ligam-se pela esquerda ao ENDIF e pela direita
                    ao próximo vértice.*/
                else {
                    /* 3.2.1 Este trecho trata a situação em que o ELSE IF tem logo após um ELSE ou ELSEIF */
                    if ($languageService->isInitialBypassCommandElseIf($vertex->getName())) {
                        if ($languageService->isInitialBypassCommandElse($this->vertices[$key + 1]->getName()) || $languageService->isInitialBypassCommandElseIf($this->vertices[$key + 1]->getName()))
                            $right = $key + 1;
                    }
                    /* 3.2.2 Este trecho procura o ENDIF no qual o ELSE ou ELSEIF deve se ligar pela esquerda
                       Percorre até o inicio da lista de vertices */
                    for ($i = $key - 1; $i >= 0; $i--) {
                        /* Se encontrar um ENDELSEIF, pular para o seu vertice de abertura*/
                        if($this->vertices[$i]->getName() === ($language->getEndVertexName().$languageService->getInitialBypassCommandElseIf())) {
                            $i = $this->vertices[$i]->getOpeningVertexIndex();
                        }
                        /* O Primeiro ENDIF que encontrar será o relacionado ao ELSE ou ELSEIF em questão*/
                        if($this->vertices[$i]->getName() === ($language->getEndVertexName().$languageService->getBypassCommandIf()['initialCommandName'])) {
                            $left = $i;
                            break;
                        }
                    }
                }
            }
            /* 4. Os vértices ENDELSEIF e ENDELSE vão se ligar pela esquerda ao vértice ENDIF */
            else if($vertex->getName() === ($language->getEndVertexName().$languageService->getInitialBypassCommandElseIf()) ||
                $vertex->getName() === ($language->getEndVertexName().$languageService->getBypassCommandElse()['initialCommandName']))
            {
                /* 4.1 Percorre do vértice até ao início da lista de vértices*/
                for($i = $key; $i >= 0; $i--) {
                    /*4.1.1 Se encontrar um vértice que feche o bloco marca a ligação a esquerda */
                    if($this->vertices[$i]->getName() === $language->getEndVertexName().$languageService->getBypassCommandIf()['initialCommandName']) {
                        $left = $i;
                        break;
                    }
                    /*4.1.2 Se encontrar um vértice que feche o bloco, o for deve pular o bloco, indo para o inicio dele*/
                    if($this->vertices[$i]->getOpeningVertexIndex() !== 0) {
                        $i = $this->vertices[$i]->getOpeningVertexIndex();
                    }
                }
            }
            /* 5. Os vértice de ENDIF liga-se a esquerda ao vértice posterior a ele, caso não seja um ELSE OU ELSEIF */
            else if($vertex->getName() === ($language->getEndVertexName().$languageService->getBypassCommandIf()['initialCommandName'])) {
                /* 5.1 Percorre a lista de vértices*/
                for($i = ($key+1); $i < count($this->vertices); $i++) {
                    /*5.1.1 Se encontrar um ELSEIF ou ELSE, verificar se abre bloco, se abrir o for deve pular o bloco*/
                    if($this->vertices[$i] instanceof Vertex && $languageService->isInitialBypassCommandElseIf($this->vertices[$i]->getName()) || $languageService->isInitialBypassCommandElse($this->vertices[$i]->getName())) {
                        /*5.1.1.1 Verifica se o vértice lido abre um bloco*/
                        if($this->containsBlockOpening($i)) {
                            /*5.1.1.1.2 Se abrir bloco, deve ser localizado o fim do bloco e atribuido o fim do bloco
                                a contagem do for.*/
                            for($j = $i; $j < count($this->vertices); $j++) {
                                if($this->vertices[$j]->getOpeningVertexIndex() === $i) {
                                    $i = $j;
                                    break;
                                }
                            }
                        }
                    }
                    /* 5.1.2 O Próximo Vértice que não for ELSEIF nem ELSE é o vértice que o ENDIF vai se ligar pela esquerda */
                    else {
                        $left = $i;
                        break;
                    }
                }
            }
            /* 6. O vértice FOR e o WHILE ligam-se pela direita ao vértice após o seu vértice de fechamento.*/
            else if($languageService->isInitialBypassCommandWhile($vertex->getName()) || $languageService->isInitialBypassCommandFor($vertex->getName())) {
                /* 6.1 Percorre os vértices até encontrar o vértice END do FOR ou do WHILE */
                for($i = $key; $i < count($this->vertices); $i++) {
                    /* 6.1.1 Marca a ligação pela direita do vértice WHILE/FOR com o vértice após o seu END */
                    if($this->vertices[$i]->getOpeningVertexIndex() == $key && $this->vertices[$i]->getName() === $language->getEndVertexName().$vertex->getName()) {
                        $right = $i+1;
                        break;
                    }
                }
            }
            /* 7. O vértice ENDFOR, ENDWHILE e ENDDO ligam-se pela direita ao seu vértice de abertura.*/
            else if($vertex->getName() === ($language->getEndVertexName().$languageService->getBypassCommandFor()['initialCommandName']) || $vertex->getName() === ($language->getEndVertexName().$languageService->getBypassCommandWhile()['initialCommandName']) || $vertex->getName() === ($language->getEndVertexName().$languageService->getBypassCommandDoWhile()['initialCommandName'])) {
                $right = $vertex->getOpeningVertexIndex();
            }
            /* 8. O vértice SWITCH possuirá mais ligações do que direita e esquerda,
            assim suas ligações são realizadas por um array de índices de seus cases/default*/
            else if($languageService->isInitialBypassCommandSwitch($vertex->getName())) {
                $commandsCaseDefaultSwitchIndexes = array();
                $endSwitchIndex = null;
                $commandDefault = false;

                /* 8.1 Percorre todos os vértices após o SWITCH e antes de seu ENDSWITCH*/
                for ($i = ($key + 1); $i < count($this->vertices); $i++) {
                    //8.1.1 passa o índice desse vértice se representar o comando CASE ou DEFAULT
                    if($languageService->isInitialBypassCommandCaseOrDefault($this->vertices[$i]->getName())) {
                        $commandsCaseDefaultSwitchIndexes[] = $i;
                    }

                    if($languageService->isInitialBypassCommandDefault($this->vertices[$i]->getName()) && !$commandDefault) {
                        $commandDefault = true;
                    }

                    //8.1.2 salva o índice de vértice de FIM do SWITCH
                    if($this->vertices[$i]->getOpeningVertexIndex() == $key && $this->vertices[$i]->getName() === $language->getEndVertexName().$languageService->getBypassCommandSwitch()['initialCommandName']) {
                        $endSwitchIndex = $i;

                        if(!$commandDefault) {
                            $left = $endSwitchIndex;
                        }

                        break;
                    }
                }

                //define as ligações do SWITCH
                $this->vertices[$key]->setMoreVertexIndexes($commandsCaseDefaultSwitchIndexes);
            }
            /* 9. Os vértices de CASE ou DEFAULT  e ENDCASE ou ENDDEFAULT (quando existirem, em caso de blocos)
               devem ser ligar pela esquerda ao FIM do SWITCH */
            else if($languageService->isInitialBypassCommandCaseOrDefault($this->removeEndVertexPrefix($vertex->getName(), $language))) {
                $containsEndCase = false;
                //9.1 verifica e marca se o CASE/DEFAULT possui ENDCASE/ENDDEFAULT
                if(in_array($key, $commandsCaseDefaultSwitchIndexes)) {
                    // 9.1.1 Percorre até o final ou até encontrar um END
                    for ($i = ($key + 1); $i < count($this->vertices); $i++) {
                        //passa o índice desse vértice se representar o comando CASE ou DEFAULT
                        if ($this->vertices[$i]->getOpeningVertexIndex() === $key) {
                            $containsEndCase = true;
                            break;
                        }
                    }
                }

                //9.2 se ele não contiver ENDCASE/ENDDEFAULT a sua ligação a esquerda será vértice do ENDSWITCH
                if(!$containsEndCase)
                    $left = $endSwitchIndex;
                //9.3 se contiver ENDCASE/ENDDEFAULT sua ligação a esquerda será com o próximo vértice
                else
                    $left = $key+1;
            }

            /* 10. Seta o objeto da Lista informando a posição dos vértices a qual eles vão se ligar*/
            $this->vertices[$key]->setRightVertexIndex($right);
            $this->vertices[$key]->setLeftVertexIndex($left);

            //inicializa novamente os índices de ligação
            $right = -1;
            $left = -1;

        }
    }

    /**
     * Define as coordenadas de cada vértice no grafo de fluxo
     *
     * @param Language $language
     */
    private function setCoordinates(Language $language)
    {
        $languageService = $this->dataCollectService->getLanguageService();
        /* INTERVALO X e Y: os espaços entre um vértice e outro será definido nessas variáveis.*/
        $distanceX = 120;
        $distanceY = 65;

        /* COORDENADA X e Y: os valores de X e Y serão armazenados nessas variáveis.*/
        $coordinateX = 0;
        $coordinateY = 0;

        $Y_big   = 0;
        $valueIF = 0;
        $aux         = 0;
        $count        = 0;
        $countTotal   = 0;
        $countBig   = 0;
        $vertexOpening = 0;
        $isEnd = false;

        /* Definindo a posição do vértice INICIO*/
        $this->vertices[0]->setX(50);
        $this->vertices[0]->setY(50);

        for($key = 1; $key < count($this->vertices); $key++) {
            /* 1. Análise dos vértices ELSE e ELSEIF*/
            if($languageService->isInitialBypassCommandElse($this->vertices[$key]->getName()) || $languageService->isInitialBypassCommandElseIf($this->vertices[$key]->getName())) {
                $Y_big   = 0;
                for($i = 0; $i < count($this->vertices); $i++) {
                    if($this->vertices[$i]->getRightVertexIndex() === $key) {
                        /* Se o vértice encontrado abrir bloco*/
                        if($this->containsBlockOpening($i)) {
                            /* Percorre até o fim do bloco desse vértice encontrado*/
                            for($j = $i; $j < count($this->vertices); $j++) {
                                if($i == $this->vertices[$j]->getOpeningVertexIndex())
                                    break;
                                /* Verifica no bloco quem tem o maior Y*/
                                if($this->vertices[$j]->getY() > $Y_big) {
                                    $Y_big = $this->vertices[$j]->getY();
                                    /* Se o vértice analisado não abrir bloco, o valor de X será o mesmo
                                    do vértice que possuir maior Y dentro do bloco.
                                    Senão será incrementado um intervalo de X.*/
                                    if(!$this->containsBlockOpening($key))
                                        $coordinateX = $this->vertices[$j]->getX() + $distanceX;
                                    else
                                        $coordinateX = $this->vertices[$i]->getX() + $distanceX;

                                    /* O valor de Y será o maior Y do bloco + o dobro do intervalo de Y*/
                                    $coordinateY = $this->vertices[$j]->getY() + ($distanceY*2);
                                }
                            }
                        }
                        /* Se o vértice encontrado não abrir bloco, o valor de X e Y será o incremento de seus intervalos*/
                        else {
                            $coordinateX = $this->vertices[$i]->getX() + $distanceX;
                            $coordinateY = $this->vertices[$i]->getY() + $distanceY;
                        }
                        break;
                    }
                }
                $Y_big   = 0;
            }

            /* 2. Trecho responsável por definir o X e Y do ENDIF*/
            else if ($this->vertices[$key]->getName() == $language->getEndVertexName().$languageService->getBypassCommandIf()['initialCommandName']) {
                $valueIF = 0;
                $count = 0;
                $countTotal = 0;
                $countBig = 0;

                /* 2.1. Localiza o valor de X do vértice que se liga ao ENDIF pela Esquerda
                   e que seja um IF ou um bloco do IF*/
                for ($i = 0; $i < $key; $i++) {
                    //verifica qual vértice e qual a coordenada do vértice que se liga a direita do ENDIF
                    if($this->vertices[$i]->getLeftVertexIndex() == $key) {
                        $valueIF = $this->vertices[$i]->getX();
                        break;
                    }
                    //verifica se o comando se liga a direita e se é um FOR ou WHILE e insere a distância do ENDFOR/ENDWHILE no valor
                    else if($this->vertices[$i]->getRightVertexIndex() == $key && ($languageService->isInitialBypassCommandFor($this->vertices[$i]->getName()) || $languageService->isInitialBypassCommandWhile($this->vertices[$i]->getName()))) {
                        for($j = 0; $j < count($this->vertices); $j++) {
                            if($this->vertices[$j]->getOpeningVertexIndex() == $i) {
                                $valueIF = $this->vertices[$j]->getX();
                                break;
                            }
                        }
                    }
                }

                /* 2.2. Aux recebe o valor de X do vértice de abertura do ENDIF*/
                $aux = $this->vertices[($this->vertices[$key]->getOpeningVertexIndex())]->getX();

                /* 2.3. Compara se o valorIF é maior que o contMaior*/
                if ($valueIF > $countBig){
                    $countBig = $valueIF;
                }

                /* 2.4. Se após o ENDIF tiver um ELSEIF ou ELSE*/
                if($languageService->isInitialBypassCommandElseIf($this->vertices[$key+1]->getName()) || $languageService->isInitialBypassCommandElse($this->vertices[$key+1]->getName())) {
                    /* Percorre do ENDIF até o final da Lista de Vértices*/
                    for ($j = $key+1; $j < count($this->vertices); $j++) {
                        /* Aux vai ser incrementado a cada comando encontrado*/
                        $aux += $distanceX;

                        /* Esse FOR vai pular qualquer bloco criado por ELSEIF ou ELSE */
                        for($k = $j; $k < count($this->vertices); $k++) {
                            if($this->vertices[$k]->getOpeningVertexIndex() == $j) {
                                /* Esse cont armazena o número de vértices contido no bloco criado*/
                                $count = $k - $j;
                                /* j = l faz com que o for externo pule o bloco*/
                                $j = $k;
                                break;
                            }
                        }

                        /* countTotal vai calcular o valor de X ao final do comando analisado*/
                        $countTotal = $aux + ($count * $distanceX);

                        /* Aqui é definido o maior valor de X encontrado*/
                        if ($countTotal > $countBig){
                            $countBig = $countTotal;
                        }

                        $countTotal = 0;
                        $count = 0;

                        /* Critério de saída do FOR, onde é o ultimo vertice a se ligar ao ENDIF*/
                        if($this->vertices[$j]->getLeftVertexIndex() == $key && ($languageService->isInitialBypassCommandElse($this->vertices[$j]->getName()) || $this->vertices[$j]->getName() == $language->getEndVertexName().$languageService->getBypassCommandElse()['initialCommandName'])) {
                            break;
                        }
                    }
                }

                /* 2.5. O valor de X do ENDIF será o maior valor de X encontrado mais o intervalo de X*/
                $coordinateX = $countBig + $distanceX;
                $aux = 0;

                /* 2.6. Localiza o vértice de abertura do ENDIF*/
                $vertexOpening = $this->vertices[$key]->getOpeningVertexIndex();

                /* 2.7. Se após o ENDIF houver um ELSEIF ou ELSE, a coordenadaY será o mesmo do vértice de abertura*/
                if($languageService->isInitialBypassCommandElseIf($this->vertices[$key+1]->getName()) || $languageService->isInitialBypassCommandElse($this->vertices[$key+1]->getName()))
                    $coordinateY = $this->vertices[$vertexOpening]->getY();
                /* Senão a coordenadaY será o valor do Y do vértice de abertura mais o intervaloY*/
                else {
                    $coordinateY = $this->vertices[$vertexOpening]->getY() + $distanceY;
                    $coordinateX = $this->vertices[$key-1]->getX() + $distanceX;
                }
            }

            /* 3. Trata os ENDELSE e ENDELSEIF*/
            else if($this->vertices[$key]->getName() == $language->getEndVertexName().$languageService->getInitialBypassCommandElseIf() || $this->vertices[$key]->getName() == $language->getEndVertexName().$languageService->getBypassCommandElse()['initialCommandName']) {
                $vertexOpening = $this->vertices[$key]->getOpeningVertexIndex();
                $coordinateY = $this->vertices[$vertexOpening]->getY();

                //verifica qual o último vértice que se liga a sua esquerda e atribui uma distância maior
                for($i = $key; $i > 0; $i--) {
                    if($this->vertices[$i]->getLeftVertexIndex() == $key) {
                        $coordinateX = $this->vertices[$i]->getX() + $distanceX;
                        break;
                    }
                }

                //se não encontrar nenhum significa que a coordenada não foi definida e receber uma distância mairo que o vértice anterior a ele
                if($coordinateX == 0) {
                    $coordinateX = $this->vertices[$key-1]->getX() + $distanceX;
                }
            }

            /* 4. Trata o comando SWITCH */
            else if($languageService->isInitialBypassCommandCaseOrDefault($this->vertices[$key]->getName())) {
                $Y_big   = 0;
                $previusCaseIndex = null;
                for($i = 0; $i < $key; $i++) {
                    $relationsIndex = $this->vertices[$i]->getMoreVertexIndexes();
                    if(count($relationsIndex) > 0 && in_array($key, $relationsIndex)) {
                        $caseIndex = array_search($key, $relationsIndex);
                        if($caseIndex > 0) {
                            $previusCaseIndex = $relationsIndex[$caseIndex-1];
                            /* Se o vértice anterior encontrado abrir bloco*/
                            if($this->containsBlockOpening($previusCaseIndex)) {
                                /* Percorre até o fim do bloco desse vértice encontrado*/
                                for($j = $previusCaseIndex; $j < count($this->vertices); $j++) {
                                    if($previusCaseIndex == $this->vertices[$j]->getOpeningVertexIndex())
                                        break;
                                    /* Verifica no bloco quem tem o maior Y*/
                                    if($this->vertices[$j]->getY() > $Y_big) {
                                        $Y_big = $this->vertices[$j]->getY();
                                        /* O valor de Y será o maior Y do bloco + o dobro do intervalo de Y*/
                                        $coordinateY = $this->vertices[$j]->getY() + ($distanceY*2);
                                    }
                                }
                            }
                            /* Se o vértice encontrado não abrir bloco, o valor de Y e será o incremento de seus intervalos*/
                            else {
                                $coordinateY = $this->vertices[$previusCaseIndex]->getY() + $distanceY;
                            }

                            $coordinateX = $this->vertices[$previusCaseIndex]->getX();
                        }
                        else if ($caseIndex == 0) {
                            $coordinateY = $this->vertices[$i]->getY() + $distanceY;
                            $coordinateX = $this->vertices[$i]->getX() + $distanceX;
                        }
                        break;
                    }
                }
                $Y_big = 0;
            }

            /* 5. Trata o comando ENDSWITCH */
            else if($this->vertices[$key]->getName() == $language->getEndVertexName().$languageService->getBypassCommandSwitch()['initialCommandName']) {
                $X_big = 0;
                $blockOpening = false;
                /* Percorre a Lista de Vértices até o vértice ENSWITCH atual */
                for ($i = 0; $i < $key; $i++) {
                    /* Encontra os vértices que ligam-se a esquerda do vértice ENDSWITCH analisado*/
                    if($this->vertices[$i]->getLeftVertexIndex() == $key) {
                        if($this->vertices[$i]->getX() > $X_big) {
                            $X_big = $this->vertices[$i]->getX();
                        }
                        $caseDefault = $languageService->getInitialBypassCommandsCaseAndDefault();
                        if(count($caseDefault) > 0 && $this->vertices[$i]->getName() == $language->getEndVertexName().$caseDefault[0] || $this->vertices[$i]->getName() == $language->getEndVertexName().$caseDefault[1]) {
                            $blockOpening = true;
                        }
                    }
                }

                if($blockOpening)
                    $coordinateX = $X_big + ($distanceX*4);
                else
                    $coordinateX = $X_big + $distanceX;

                $coordinateY = $this->vertices[$this->vertices[$key]->getOpeningVertexIndex()]->getY();
                $X_big = 0;
            }

            /* . Trecho responsável por definir X e Y dos demais vértices*/
            else {
                /* Percorre a Lista de Vértices*/
                for ($i = 0; $i < count($this->vertices); $i++) {
                    /* Encontra o vértice que liga-se a esquerda do vértice analisado*/
                    if($this->vertices[$i]->getLeftVertexIndex() == $key) {
                        /* ===> Se for um IF que abre bloco e sem ELSE IF e ELSE assume outro valor para Y*/

                        /* .1 TRECHO DE CÓDIGO QUE DEFINE O VALOR DE Y PARA VÉRTICES QUE FICAM DENTRO DE UM IF SEM ELSE
                                Se esse vértice encontrado for um IF*/
                        if($languageService->isInitialBypassCommandIf($this->vertices[$i]->getName())) {
                            /* Percorre a lista de vértices a procura do vértice que possui o IF como vértice de abertura*/
                            for($j = 0; $j < count($this->vertices); $j++) {
                                if($this->vertices[$j]->getOpeningVertexIndex() == $i) {
                                    /* Se esse IF analisado não possuir um ELSEIF ou ELSE, então o valor de Y será o valor do Y do IF + o intervalo*/
                                    if (!$languageService->isInitialBypassCommandElseIf($this->vertices[$j+1]->getName()) && !$languageService->isInitialBypassCommandElse($this->vertices[$j+1]->getName())) {
                                        $coordinateY = $this->vertices[$i]->getY() + $distanceY;
                                        break;
                                    }
                                }
                            }
                        }

                        /* Se o comando é FOR ou WHILE */
                        if($languageService->isInitialBypassCommandFor($this->vertices[$i]->getName()) || $languageService->isInitialBypassCommandWhile($this->vertices[$i]->getName()) || $languageService->isInitialBypassCommandDoWhile($this->vertices[$i]->getName())) {
                            $coordinateY = $this->vertices[$i]->getY() + $distanceY;
                        }

//                        //Se o comando é um ENDFOR
                        if($this->vertices[$key]->getName() == $language->getEndVertexName().$languageService->getBypassCommandFor()['initialCommandName'] || $this->vertices[$key]->getName() == $language->getEndVertexName().$languageService->getBypassCommandWhile()['initialCommandName'] || $this->vertices[$key]->getName() == $language->getEndVertexName().$languageService->getBypassCommandDoWhile()['initialCommandName']) {
//                        if(strpos($this->vertices[$key]->getName(), $language->getEndVertexName()) !== false && $this->vertices[$key]->getName() !== $language->getEndVertexName()) {
                            $coordinateY = $this->vertices[($this->vertices[$key]->getOpeningVertexIndex())]->getY();
                        }

                        /* .2 A coordenadaX será o valor de X do vértice de origem*/
                        $coordinateX = $this->vertices[$i]->getX() + $distanceX;

                        /* Se o Y ainda não foi definido, ele será o mesmo Y do vértice de origem*/
                        if($coordinateY == 0)
                            $coordinateY = $this->vertices[$i]->getY();
                        break;
                    }
                    //verifica se o comando se liga a direita e se é um FOR ou WHILE e insere a distância do ENDFOR/ENDWHILE no valor
                    else if($this->vertices[$i]->getRightVertexIndex() == $key && ($languageService->isInitialBypassCommandFor($this->vertices[$i]->getName()) || $languageService->isInitialBypassCommandWhile($this->vertices[$i]->getName()))) {
                        for ($j = 0; $j < count($this->vertices); $j++) {
                            if ($this->vertices[$j]->getOpeningVertexIndex() == $i) {
                                $coordinateX = $this->vertices[$j]->getX() + $distanceX;
                                break;
                            }
                        }

                        if ($this->vertices[$key]->getName() != $language->getEndVertexName() . $languageService->getBypassCommandFor()['initialCommandName'] && $this->vertices[$key]->getName() != $language->getEndVertexName() . $languageService->getBypassCommandWhile()['initialCommandName'] && $this->vertices[$key]->getName() != $language->getEndVertexName() . $languageService->getBypassCommandDoWhile()['initialCommandName']) {
                            $coordinateY = $this->vertices[$j]->getY();
                        }
                        else {
                            $coordinateY = $this->vertices[$this->vertices[$key]->getOpeningVertexIndex()]->getY();
                        }
                        break;
                    }

                    /* Caso do if sem else e sem else if*/
                    if(!$languageService->isInitialBypassCommandElseIf($this->vertices[$key]->getName()) && !$languageService->isInitialBypassCommandElse($this->vertices[$key]->getName()) && $this->vertices[$i]->getRightVertexIndex() == $key && $languageService->isInitialBypassCommandIf($this->vertices[$i]->getName())) {
                        $coordinateY = $this->vertices[$i]->getY();
                    }
                }
            }

            $this->vertices[$key]->setX($coordinateX);
            $this->vertices[$key]->setY($coordinateY);

            $coordinateX = 0;
            $coordinateY = 0;
        }

//        $arrayResult = array();
//        foreach ($this->vertices as $key => $value) {
//            if ($value instanceof Vertex) {
////                    $valor = [
////                        'name' => $value->getName(),
////                        'openingVertexIndex' => $value->getOpeningVertexIndex(),
////                        'lineNumber' => $value->getEndLineNumber()
////                    ];
////                    $setValue = $value->toArray();
////                    $setValue['id'] = $key;
////                    $arrayResult[] = $setValue;
//                $arrayResult[] = $value->toArray();
//            }
//        }
//        \Zend\Debug\Debug::dump($arrayResult);

    }

    /**
     * Indica se o vértice presente em um determinado índice possui dentro em seu bloco de comandos outros vértices
     * @param int $index
     * @return bool
     */
    private function containsBlockOpening($index)
    {
        /* Percorre a lista de comandos de desvio do código */
        foreach ($this->codeCommands as $key => $codeCommand) {
            /* Encontra qual é o comando referente ao index vértice que está sendo verificado e
               verifica se o próximo comando após esse vértice é de abertura de bloco */
            if($codeCommand instanceof CodeBypassCommand && $codeCommand->getReferentVertexIndex() == $index && $this->codeCommands[($key+1)]->getName() == "{") {
                return true;
            }
        }
        return false;
    }

    /**
     * Localiza a posição de fechamento de bloco (}) criado por uma abertura de bloco ({)
     * @param int $blockStartIndex
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

    /**
     * Remove o prefixo END dos vértices de final de bloco
     *
     * @param string $vertexName
     * @param Language $language
     * @return string
     */
    private function removeEndVertexPrefix($vertexName, Language $language)
    {
        return str_replace($language->getEndVertexName(), "", $vertexName);
    }
}