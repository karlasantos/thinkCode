<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Service;

use Doctrine\ORM\EntityManager;
use SourceCode\Model\Entity\BypassCommand;
use SourceCode\Model\Entity\DataType;
use SourceCode\Model\Entity\LogicalConnective;
use SourceCode\Model\Entity\SourceCode;
use SourceCode\Model\Entity\SpecialCharacter;
use SourceCode\Model\CodeBypassCommand;
use SourceCode\Service\Language as LanguageService;
use SourceCode\Model\Entity\Language as LanguageEntity;

/**
 * Class DataCollect
 * Realiza a coleta dos dados necessários para a análise
 * @package SourceCode\Service
 */
class DataCollect
{
    /**
     * Gerenciador de entidades do Doctrine
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Serviço responsável pelos dados da linguagem
     *
     * @var LanguageService
     */
    private $languageService;

    /**
     * Armazena os objetos dos comandos de desvio encontrados no código fonte (Class SourceCode\Model\CodeBypassCommand)
     * @var array
     */
    private $codeCommands;

    /**
     * Armazena o nome dos comandos de desvio encontrados no código fonte
     * @var array
     */
    private $codeCommandsName;

    /**
     * Quantidade de linhas úteis do código fonte
     * @var integer
     */
    private $usefulLineCounter;

    /**
     * Quantidade de variáveis declaradas no código fonte
     * @var integer
     */
    private $variableCounter;

    /**
     * Quantidade de conectivos lógicos utilizados no código fonte
     * @var integer
     */
    private $logicalConnectiveCounter;

    /**
     * DataCollect constructor.
     * Inicializa todas as variáveis do service
     * @param EntityManager $entityManager
     * @param LanguageService $languageService
     */
    public function __construct(EntityManager $entityManager, LanguageService $languageService)
    {
        $this->entityManager            = $entityManager;
        $this->codeCommands             = array();
        $this->codeCommandsName         = array();
        $this->usefulLineCounter        = 0;
        $this->variableCounter          = 0;
        $this->logicalConnectiveCounter = 0;
        $this->languageService = $languageService;
    }

    /**
     * Retorna uma array de objetos dos comandos de desvio contidos no código
     *
     * @return array
     */
    public function getCodeCommands()
    {
        return $this->codeCommands;
    }

    /**
     * Retorna um array com os nomes dos comandos de desvio contidos no código
     *
     * @return array
     */
    public function getCodeCommandsName()
    {
        return $this->codeCommandsName;
    }

    /**
     * Retorna a quantidade de linhas úteis do código
     *
     * @return int
     */
    public function getUsefulLineCounter()
    {
        return $this->usefulLineCounter;
    }

    /**
     * Retorna a quantidade de variáveis declaradas no código
     *
     * @return int
     */
    public function getVariableCounter()
    {
        return $this->variableCounter;
    }

    /**
     * Retorna a quantidade de conectivos lógicos utilizados no código
     *
     * @return int
     */
    public function getLogicalConnectiveCounter()
    {
        return $this->logicalConnectiveCounter;
    }

    /**
     * Retorna o Language Service
     *
     * @return LanguageService
     */
    public function getLanguageService()
    {
        return $this->languageService;
    }

    /**
     * Retira os dados dos comandos de desvio do código
     *
     * @param SourceCode $sourceCode
     * @return array
     * @throws \Exception
     */
    public function getDataFromCode(SourceCode $sourceCode)
    {
        $language = $sourceCode->getLanguage();
        //cria um array onde cada índice contém uma linha do código fonte (explode a string pelo caracter \n)
        $codeContent = explode(PHP_EOL, $sourceCode->getContent());
        //indica se a linha contém texto
        $isText = false;
        //indica se a linha contém comentário
        $isComment = false;
        //indica se o código possui a estrutura inicial de código
        $removeLastKey = false;
        //linha corrente analisada
        $lineNumber = 0;
        //caracter anterior ao atual no laço de repetição de caracteres
        $previusCharacter = "";
        $previusToken = "";
        //token a ser inserido na lista de comandos
        $token = "";

        /* 0.1 - Variável para tratamento do DO-WHILE */
        //armazena o token que representa o terminal do comando do-while
        $terminalDoWhile = null;

        /* 0.2 - Variáveis para tratamento do SWITCH */
        //armazena o token que representa o terminal do comando switch
        $terminalSwitch = null;
        //quantidade de comandos case/default dentro do switch
        $lengthCaseAndDefault = 0;
        //contador de case ou defaults já inseridos nos comandos de desvio
        $countCaseAndDefault = 0;
        //armazena o último comando da lista de cases do switch lido no código
        $lastCommandCase = null;
        //armazena índice o último comando da lista de cases do switch lido no código
        $lastCommandCaseIndex = null;

        /* 0.3 - Variáveis para tratamento das declarações de variáveis através dos tipos de dados */
        //indica se uma determinada linha contém um tipo de dado
        $lineContainsDataType = false;
        //token de controle do tipo de dados que indica se é a declaração de uma variável
        $dataType = "";

        /* indica se um determinado token deve ser inserido na listagem de comandos de desvio do código
           usado para casos em que um terminal de comando também é inicial de comando
        */
        $addToken = true;

        //percorre as linhas do código
        foreach($codeContent as $lineKey => $line) {
            $lineNumber++;
            $isText = false;
            $isComment = false;
            //transforma todos os caracteres da linha em minúsculo
            $line = strtolower($line) . " ";

            //remove os tabs que causam erros na análise
            $line = str_replace("\t", " ",$line);

            //quebra a linha em um array de caracteres caracteres
            $characters = str_split($line);

            //se a linha não contém nenhum comando de desvio marca texto e comentário
            if(!$isComment && !$this->lineContainsInitialBypassCommand($line) && !$this->lineContainsTerminalBypassCommand($line) && strpos($line, $language->getEndCodeStructure()) === false) {
                $isText = true;
            }

            //verifica se a linha contém a estrutura de início de código
            if(!$removeLastKey && $this->lineContainsInitialCodeStructure($line, $language->getInitialCodeStructure())) {
                $removeLastKey = true;
                $this->usefulLineCounter++;
//                \Zend\Debug\Debug::dump("remove last key IF:");
//                \Zend\Debug\Debug::dump($removeLastKey);
                continue;
            } else {
                //identifica se a linha contém tipo de dados, isso indica que a linha contém declaração de variável
                $lineContainsDataType = $this->lineContainsDataType($line);
            }
//            print_r("-------------------------------------------------------------------------------------------------");

             /* Verifica se a linha não possui o token de terminal do comando do do-while,
              que deve ser ignorado na adição */
            if(!$this->lineContainsToken($line, $terminalDoWhile)) {
                $addToken = true;
            }
            /*se a linha contém o terminal do do-while e contém ; significa que o terminal foi encontrado
             e não deve ser adicionado como token de desvio */
            else if($this->lineContainsToken($line, ";")){
                $addToken = false;
            } else {
                $addToken = true;
            }

            // Verifica se a linha contém o comando de switch e conta os cases dentro desse switch
            if($this->lineContainsToken($line, $this->languageService->getBypassCommandSwitch()['initialCommandName'])) {
                for ($i = $lineKey+1; $i < count($codeContent); $i++) {
                    if($this->lineContainsInitialBypassCommandCaseOrDefault($codeContent[$i]))
                        $lengthCaseAndDefault++;
                    else if($this->lineContainsToken($codeContent[$i], $this->languageService->getBypassCommandSwitch()['initialCommandName']))
                        break;
                }
            }

            //se a linha não contiver um comentário incrementa o contador de linhas úteis
            if(!$isComment) {
                $this->usefulLineCounter++;
            }

            //acumula o número de conectivos lógicos do código
            $this->logicalConnectiveCounter += $this->numberOfLogicalConnectivesInLine($line);

            //Percorre os caracteres de cada linha
            foreach ($characters as $keyChar => $character) {
                // 1 - Se não houve comentário e o caracter lido for uma aspas marca como texto
                if(!$isComment && $character === '"')
                    $isText = true;

                // 2 - Se o caracter lido não estiver em um texto
                if(!$isText) {
                    // 2.1 - /* caracteriza início de comentário
                    if($character === "*" && $previusCharacter === "/")
                        $isComment = true;

                    // 2.2 - */ caracteriza fim de comentário
                    if($character === "/" && $previusCharacter === "*")
                        $isComment = false;

                    // 2.3 - // caracteriza comentário somente na linha
                    if($character === "/" && $previusCharacter === "/")
                        break;
                }

                // Armazena o caracter lido anteriormente
                $previusCharacter = $character;
//
//                \Zend\Debug\Debug::dump('################');
//                \Zend\Debug\Debug::dump('token:'. $token .".");
//                \Zend\Debug\Debug::dump(array_slice($characters, 0, ($keyChar+1)));
//                \Zend\Debug\Debug::dump('character:'. $character.".");
//                \Zend\Debug\Debug::dump('!TEXT && !COMMENT:'. (string)(!$isComment && !$isText).".");
//                \Zend\Debug\Debug::dump('ADD TOKEN:'. $addToken);
//                \Zend\Debug\Debug::dump('---- TOKENS -----');
//                $arrayResult = array();
//                foreach ($this->codeCommands as $value) {
//                    if($value instanceof CodeBypassCommand)
//                        $arrayResult[] = $value->getName();
//                }
//                \Zend\Debug\Debug::dump($arrayResult);

                // 3 - Se não é comentário e texto o caracter é parte do código efetivo.
                if(!$isComment && !$isText) {
                    /* 3.1 - Se o caracter for um espaço ou um caracter especial significa
                    que o token terminou e deve ser adicionado na lista de comandos de desvio do código */
                    if($this->languageService->isSpecialCharacter($character) || $character === " ") {
                        // 3.1.1 - Se a ação de adição de token estiver marcada efetua a análise e a inserção do token
                        if($addToken) {
                            /* Para os casos da linguagem C,
                              que possui um caractere especial (}) como terminal de comando de desvio */
                            if ($this->languageService->isTerminalBypassCommand($character) && $token == "") {
//                                \Zend\Debug\Debug::dump(' $token = $character' . $character . ".");
                                $token = $character;
                            }

                            /* 3.1.1.3 - Tratar switch-case */
                            //Verifica se o comando é o token representa o switch e salva seu terminal
                            if($this->languageService->isInitialBypassCommandSwitch($token)) {
                                $terminalSwitch = $this->languageService->getBypassCommandSwitch()['terminalCommandName'];
                            }
                            //se possui terminal salvo ou se o token é um case ou default salva o token case/default e o token terminal do switch
                            else if($this->languageService->isInitialBypassCommandCaseOrDefault($token)) {
                                $lastCommandCase = $token;
                                end($this->codeCommandsName);
                                //se o penúltimo comando não for um case ou default significa que ele possui bloco, o default será adicionado duas vezes
                                if(!$this->languageService->isInitialBypassCommandCaseOrDefault($this->codeCommandsName[key($this->codeCommandsName)-1]))
                                    $lastCommandCaseIndex = key($this->codeCommandsName)+2;
                                else
                                    $lastCommandCaseIndex = key($this->codeCommandsName);
                                $terminalSwitch = $this->languageService->getBypassCommandSwitch()['terminalCommandName'];
                                $countCaseAndDefault++;
                            }
                            //todo VERIFICAR ESSA CONDIÇÃO
                            /*verifica se possui último comando case e se o token atual é o terminal do switch e adiciona o terminal do switch
                             na lista de tokens de desvio do código */
                            else if ($lastCommandCase !== null && $this->languageService->isTerminalBypassCommandSwitch($token) && $terminalSwitch !== null) {
                                //verifica se a quantidade de cases e defaults do switch já foi adicionada e adiciona mais um terminal do switch
                                if($countCaseAndDefault > 0 && $countCaseAndDefault == $lengthCaseAndDefault) {
                                    /*retorna uma parcela do array de nomes comandos adicionados
                                     como comando de desvio do código iniciando pelo último comando case adicionado */
                                    $commandsAfterLastCase = array_slice($this->codeCommandsName, $lastCommandCaseIndex);
                                    //conta as aberturas de bloco contidas nos comandos de desvio do código (usa-se "{" por padrão)
                                    $blockOpeningLength = count(array_keys($commandsAfterLastCase, "{"));
                                    //conta os fechamentos de bloco contidos nos comandos de desvio do código (usa-se "}" por padrão)
                                    $blockClosureLength = count(array_keys($commandsAfterLastCase, "}"));
//                                    \Zend\Debug\Debug::dump($this->codeCommandsName);
//                                    \Zend\Debug\Debug::dump($lastCommandCaseIndex);
//                                    \Zend\Debug\Debug::dump($commandsAfterLastCase);
//                                    \Zend\Debug\Debug::dump('$blockOpeningLength - $blockClosureLength');
//                                    \Zend\Debug\Debug::dump($blockOpeningLength);
//                                    \Zend\Debug\Debug::dump($blockClosureLength);
                                    /*se a diferença das aberturas e fechamentos de blocos for igual a 1,
                                     indica que o um fechamento do bloco a mais para o switch deve ser adicionado,
                                     porque o case/default pode utilizar/utiliza o mesmo terminal de comando do switch,
                                     sendo necessária uma adição a mais */
                                    if(($blockOpeningLength - $blockClosureLength) == 1) {
                                        //adiciona o token de fechamento a mais
                                        $this->addToken($terminalSwitch, $lineNumber);
                                        //reinicializa as variáveis de controle
                                        $lastCommandCase = null;
                                        $lastCommandCaseIndex = null;
                                        $terminalSwitch = null;
                                        $countCaseAndDefault = 0;
                                        $lengthCaseAndDefault = 0;
                                    }
                                }
                            }

                            // 3.1.1.1 - Este caso é para tratar o ELSE IF da Linguagem C
                            if ($this->languageService->isInitialBypassCommandElse($previusToken) && $this->languageService->isInitialBypassCommandIf($token) && $language->getName() == "Linguagem C") {
                                //remove o comando de abertura de bloco "{"
                                array_pop($this->codeCommands);
                                array_pop($this->codeCommandsName);
                                //remove o comando de desvio else
                                array_pop($this->codeCommands);
                                array_pop($this->codeCommandsName);

                                //envia os tokens concatenados
                                $this->addToken($previusToken . $token, $lineNumber);
                            }
                            else {
                                $this->addToken($token, $lineNumber);
                            }

                            /* 3.1.1.2 - Tratar do-while:
                              Verifica se o comando representado pelo token é um comando que representa o comando inicial
                              do-while na linguagem e se seu terminal é um comando também inicial na liguagem (caso da Linguagem C)
                              para definir uma exceção para esse comando não ser adicionado na lista de comandos de desvio do código
                              duas vezes
                             */
                            if($this->languageService->isInitialBypassCommandDoWhile($token) && $this->languageService->terminalBypassCommandDoWhileIsAlsoInitial()) {
                                $terminalDoWhile = $this->languageService->getBypassCommandDoWhile()['terminalCommandName'];
                            }

                            //todo essa condição foi realizada apenas para essa linguagem e deve ser revista
                            //todo NÃO FUNCIONA
//                            if($language->getName() == 'Linguagem C') {
//                                end($this->codeCommandsName);
//                                $lastCommandTokens = $this->codeCommandsName[key($this->codeCommandsName)-1];
//                                if($lastCommandTokens == "if") {
////                                    \Zend\Debug\Debug::dump('------------');
////                                    \Zend\Debug\Debug::dump('$lastCommandTokens');
////                                    \Zend\Debug\Debug::dump($lastCommandTokens);
////                                    \Zend\Debug\Debug::dump('condition if');
////                                    \Zend\Debug\Debug::dump(($this->isInitialBypassCommand($lastCommandTokens) || $lastCommandTokens == "elseif" && !$this->isBypassCommandCaseOrDefault($lastCommandTokens) && !$this->isBypassCommandSwitch($lastCommandTokens) && !$this->isBypassCommandDoWhile($lastCommandTokens)) && $previusCharacter == ")" && ((isset($characters[$keyChar+1]) && $characters[$keyChar+1] !== "{") && !$this->lineContainsToken($codeContent[($lineKey+1)], "{")));
////                                    \Zend\Debug\Debug::dump('$codeContent[($lineKey+1)]');
////                                    \Zend\Debug\Debug::dump($codeContent[($lineKey + 1)]);
//                                }
//                                //verifica se um token de fechamento deve ser adicionado pela falta de chaves nos códigos em C
//                                if($token == $lastCommandTokens && ($this->isInitialBypassCommand($lastCommandTokens) || $lastCommandTokens == "elseif" && !$this->isBypassCommandCaseOrDefault($lastCommandTokens) && !$this->isBypassCommandSwitch($lastCommandTokens) && !$this->isBypassCommandDoWhile($lastCommandTokens)) && $previusCharacter == ")" && ((isset($characters[$keyChar+1]) && $characters[$keyChar+1] !== "{") && !$this->lineContainsToken($codeContent[($lineKey+1)], "{"))) {
//                                    $this->addToken("}", $lineNumber);
//
//                                }
//                            }

                            // 3.1.1.4 - salva o token anterior somente se o caracter for um espaço e se o token estiver preenchido
                            if ($character === " " && !empty($token)) {
                                $previusToken = $token;
                            }
                            // 3.1.1.5 - Se for um caracter especial o tokenAnt recebe vazio
                            else if ($this->languageService->isSpecialCharacter($character))
                                $previusToken = "";

                        }
                        /* 3.1.2 - Se addToken for falso significa que o terminal de comando desvio foi encontrado e não deve ser adicionado,
                          podendo ser inicializado novamente
                        */
                        else if ($token === $terminalDoWhile && !empty($terminalDoWhile)) {
                            $this->addTokenTerminalDoWhile($lineNumber);
                            $terminalDoWhile = null;
                            $addToken = true;
                        }
                        $token = "";
                    }
                    /* 3.2 - Se não for um caracter especial significa que o token ainda não terminou de ser montado
                      então incrementa caracter por caracter lido a variável token
                    */
                    else {
                        $token .= $character;
                    }
                }

                //verifica se a linha contém uma declaração de variável
                if($lineContainsDataType) {
                    /*se o caracter lido for um espaço ou caracter especial como ";"
                     realiza a contagem de variáveis de acordo com a quantidade de vírgulas na linha
                      - Soma-se sempre 1 porque o número de variáveis sempre será um número maior que o número de vírgulas */
                    if($character == " " || $this->languageService->isSpecialCharacter($character)) {
                        if(in_array($dataType, $this->languageService->getElementsOfLanguage()['dataTypes'])) {
                            $numberOfVariablesInLine = substr_count($line, ",") + 1;
                            $this->variableCounter += $numberOfVariablesInLine;
                        }
                        //depois da contagem inicializa o tipo de dados
                        $dataType = "";
                    }
                    /* Se não for nenhum caracter especial apenas concatena o caracter no controle de tipo de dados */
                    else {
                        $dataType .= $character;
                    }
                }
            }
        }

//        \Zend\Debug\Debug::dump("last key remove: ");
//        var_dump($removeLastKey);
//
//        \Zend\Debug\Debug::dump('---- TOKENS -----');
//        $arrayResult = array();
//        foreach ($this->codeCommands as $value) {
//            if($value instanceof CodeBypassCommand)
//                $arrayResult[] = $value->getName();
//        }
//        \Zend\Debug\Debug::dump($arrayResult);

        //verifica se a última chave deve ser removida (chave que indica o fechamento do código)
        if($removeLastKey && !empty($this->codeCommands)) {
            array_pop($this->codeCommands);
            array_pop($this->codeCommandsName);
        }

        // Será adicionado um ponto no final para atender a classe ctrlAnalisaEstrutura,
        // simbolizando o fim do vetor.
        $this->addToken(".", 0);
//
//        \Zend\Debug\Debug::dump('---- TOKENS -----');
//        $arrayResult = array();
//        foreach ($this->codeCommands as $value) {
//            if($value instanceof CodeBypassCommand)
//                $arrayResult[] = $value->toArray();
//        }
//        \Zend\Debug\Debug::dump($arrayResult);
//        die();

//        \Zend\Debug\Debug::dump('$this->logicalConnectiveCounter');
//        \Zend\Debug\Debug::dump($this->logicalConnectiveCounter);
//        \Zend\Debug\Debug::dump('$this->usefulLineCounter');
//        \Zend\Debug\Debug::dump($this->usefulLineCounter);
//        \Zend\Debug\Debug::dump('$this->variableCounter');
//        \Zend\Debug\Debug::dump($this->variableCounter);

        //todo adicionar uma visualização do array

        return $this->codeCommands;
    }

    /**
     * Informa se uma linha contém comando de desvio
     *
     * @param string $line
     * @return bool
     */
    private function lineContainsInitialBypassCommand($line)
    {
        //transforma o token para minúsculas
        $line = strtolower($line);
        foreach ($this->languageService->getElementsOfLanguage()['diversionCommands'] as $bypassCommand) {
            if(strpos($line, $bypassCommand['initialCommandName']) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Informa se uma linha contém comando de desvio
     *
     * @param string $line
     * @return bool
     */
    private function lineContainsTerminalBypassCommand($line)
    {
        //transforma a linha para minúsculas
        $line = strtolower($line);
        foreach ($this->languageService->getElementsOfLanguage()['diversionCommands'] as $bypassCommand) {
            $terminalCommandNames = explode("|", $bypassCommand['terminalCommandName']);
            foreach ($terminalCommandNames as $terminalCommandName) {
                if (strpos($line, $terminalCommandName) !== false) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Verifica se a linha contém a estrutura inicial do código
     *
     * @param string $line
     * @param string $initialCodeStructures
     * @return bool
     */
    private function lineContainsInitialCodeStructure($line, $initialCodeStructures)
    {
        $initialCodeStructures = explode("|", $initialCodeStructures);
        foreach ($initialCodeStructures as $initialCodeStructure) {
            if(strpos($line, $initialCodeStructure) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifica se uma determinada linha contém um determinado token
     *
     * @param string $line
     * @param string $token
     * @return bool
     */
    private function lineContainsToken($line, $token = null)
    {
        //transforma o token e a linha para minúsculas
        $line = strtolower($line);
        if($token != null) {
            $token = strtolower($token);
            if (strpos($line, $token) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se uma determinada linha contém os comandos de desvio case ou default
     *
     * @param string $line
     * @return bool
     */
    private function lineContainsInitialBypassCommandCaseOrDefault($line)
    {
        $commandNamesCaseDefault = $this->languageService->getInitialBypassCommandsCaseAndDefault();
        foreach ($commandNamesCaseDefault as $commandName) {
            if(strpos($line, $commandName) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifica se a linha contém um tipo de dados
     *
     * @param string $line
     * @return bool
     */
    private function lineContainsDataType($line)
    {
        foreach ($this->languageService->getElementsOfLanguage()['dataTypes'] as $dataType) {
            if(strpos($line, $dataType) !== false) {
                return true;
            }
        }
        return false;
    }
    /**
     * Verifica o número de conectivos lógicos contidos na linha
     * @param string $line
     * @return int
     */
    private function numberOfLogicalConnectivesInLine($line)
    {
        $specialCharacterCounter = 0;
        foreach ($this->languageService->getElementsOfLanguage()['logicalConnectives'] as $logicalConnective) {
            $index = strpos($line, $logicalConnective);
            if($index !== false) {
                $valueIndex = $index;
                if($index > 0) {
                    $valueIndex = $index-1;
                }
                if($line[$valueIndex] == " " && $line[$index+1] == " ") {
                    $specialCharacterCounter += substr_count($line, $logicalConnective);
                }
            }
        }
        return $specialCharacterCounter;
    }

    /**
     * Verifica se um determinado token é o terminal de comando do último comando de desvio adicionado a lista de comandos do código
     *
     * @param CodeBypassCommand $previusBypassCommand
     * @param string $token
     * @return bool
     */
    private function isTerminalBypassCommandLastCodeCommand(CodeBypassCommand $previusBypassCommand, $token)
    {
        $terminalCommands = array();

        if($previusBypassCommand instanceof CodeBypassCommand) {
            //obtém apenas os nomes iniciais dos comandos de desvio da linguagem
            $initialCommandNames = array_column($this->languageService->getElementsOfLanguage()['diversionCommands'], 'initialCommandName');
            /*identifica o índice do comando de desvio que representa
             o $previusBypassCommand nos comandos de desvio através de seu nome */
            $indexOfElement = array_search($previusBypassCommand->getName(), $initialCommandNames);
            //retorna o terminal do comando de desvio que representa o $previusBypassCommand na linguagem
            $terminalCommands = explode("|", $this->languageService->getElementsOfLanguage()['diversionCommands'][$indexOfElement]['terminalCommandName']);
        }
        return in_array($token, $terminalCommands);
    }

    /**
     * Adiciona o token na lista de comandos de desvio do código
     * @param string $token
     * @param integer $lineNumber
     */
    private function addToken($token, $lineNumber)
    {
//        \Zend\Debug\Debug::dump('addToken: '.$token);

        end($this->codeCommands);
        $lastIndex = key($this->codeCommands);
        $lastCommand = &$this->codeCommands[$lastIndex]; //retorno por referência
        $lastButOne = &$this->codeCommands[($lastIndex-1)];
        $previusCommandName = null;

        if($lastCommand instanceof CodeBypassCommand)
            $previusCommandName = $lastCommand->getName();

        /* - Se o token for um token de término de comando de desvio e o último da lista for um {, então remove da lista o {
           para que permaneçam na lista de vértices apenas as chaves que tiverem comandos aninhados
        */
        if($this->languageService->isTerminalBypassCommand($token) && $lastCommand instanceof CodeBypassCommand && $lastCommand->getName() === "{") {
            if($this->isTerminalBypassCommandLastCodeCommand($lastButOne, $token) && (!$this->languageService->isInitialBypassCommandDoWhile($lastButOne->getName()) || ($this->languageService->isInitialBypassCommandDoWhile($lastButOne->getName()) && !$this->languageService->terminalBypassCommandDoWhileIsAlsoInitial()))) {
                //salva a linha inicial do último comando
                if ($lastCommand instanceof CodeBypassCommand)
                    $initialLineNumber = $lastCommand->getInitialLineNumber();

                //remove o último elemento da lista de comandos que seria uma chave "{"
                array_pop($this->codeCommands);
                //remove do array de nomes de comandos
                array_pop($this->codeCommandsName);

                //recalcula os tamanhos e adquire o novo último comando da lista de comandos de desvio do código
                end($this->codeCommands);
                $lastIndex = key($this->codeCommands);
                $lastCommand = &$this->codeCommands[$lastIndex]; //retorno por referência

                //define a linha inicial do comando para a linha inicial do comando "{" que foi removido
                if ($lastCommand instanceof CodeBypassCommand) {
                    $lastCommand->setInitialLineNumber($initialLineNumber);
                    $lastCommand->setEndLineNumber($lineNumber);
                }
            }
        }
        //se token for um término de comando de desvio e o anterior for diferente de "{" adiciona o fechamento de bloco
        else if(($this->languageService->isTerminalBypassCommand($token) && $lastCommand instanceof CodeBypassCommand && $lastCommand->getName() !== "{") || ($token == $this->languageService->getEndCodeStructure() && !$this->languageService->isTerminalBypassCommand($this->languageService->getEndCodeStructure()))) {
            //cria o comando fechamento de bloco
            $endBlockCommand = new CodeBypassCommand();
            $endBlockCommand->setName("}");
            $endBlockCommand->setInitialLineNumber($lineNumber);

            array_push($this->codeCommands, $endBlockCommand);
            array_push($this->codeCommandsName, "}");
        }

        // Se o token lido for um Comando início de desvio então deve ser armazenado na lista de comandos.
        if($this->languageService->isInitialBypassCommand($token)) {
            //cria o comando de desvio
            $codeBypassCommand = new CodeBypassCommand();
            $codeBypassCommand->setName($token);
            $codeBypassCommand->setInitialLineNumber($lineNumber);

            //cria o comando de início de bloco
            $initialBlockCommand = new CodeBypassCommand();
            $initialBlockCommand->setName("{");
            $initialBlockCommand->setInitialLineNumber($lineNumber);

            //adiciona o comando de desvio e o comando de início de bloco nos últimos índices do array de comandos de desvio do código
            array_push($this->codeCommands, $codeBypassCommand, $initialBlockCommand);
            array_push($this->codeCommandsName, $token, "{");
        } else if($token === ".") { //verifica se é o caractere que indica o final da lista de comandos
            //cria o indicador de final de comandos
            $endCodeBypassCommand = new CodeBypassCommand();
            $endCodeBypassCommand->setName($token);
            $endCodeBypassCommand->setInitialLineNumber($lineNumber);
            array_push($this->codeCommands, $endCodeBypassCommand);
            array_push($this->codeCommandsName, $token);
        }
    }

    /**
     * Adiciona o token de terminal de comando Do While
     * @param integer $lineNumber
     */
    private function addTokenTerminalDoWhile($lineNumber) {
        end($this->codeCommands);
        $lastIndex = key($this->codeCommands);
        $lastCommand = &$this->codeCommands[$lastIndex]; //retorno por referência
        $initialLineNumber = null;

        /* - Se o token for um token de término de comando de desvio e o último da lista for um {, então remove da lista o {
           para que permaneçam na lista de vértices apenas as chaves que tiverem comandos aninhados
        */
        if($lastCommand instanceof CodeBypassCommand && $lastCommand->getName() === "{") {
            //salva a linha inicial do último comando
            if ($lastCommand instanceof CodeBypassCommand)
                $initialLineNumber = $lastCommand->getInitialLineNumber();

            //remove o último elemento da lista de comandos que seria uma chave "{"
            array_pop($this->codeCommands);
            //remove do array de nomes de comandos
            array_pop($this->codeCommandsName);

            //recalcula os tamanhos e adquire o novo último comando da lista de comandos de desvio do código
            end($this->codeCommands);
            $lastIndex = key($this->codeCommands);
            $lastCommand = &$this->codeCommands[$lastIndex]; //retorno por referência

            //define a linha inicial do comando para a linha inicial do comando "{" que foi removido
            if ($lastCommand instanceof CodeBypassCommand) {
                $lastCommand->setInitialLineNumber($initialLineNumber);
                $lastCommand->setEndLineNumber($lineNumber);
            }
        } else {
            //cria o comando fechamento de bloco
            $endBlockCommand = new CodeBypassCommand();
            $endBlockCommand->setName("}");
            $endBlockCommand->setInitialLineNumber($lineNumber);

            array_push($this->codeCommands, $endBlockCommand);
            array_push($this->codeCommandsName, "}");
        }
    }
}