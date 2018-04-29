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
use SourceCode\Entity\SpecialCharacter;
use SourceCode\Model\CodeBypassCommand;

/**
 * Class DataCollect
 * Realiza a coleta dos dados necessários para a análise
 * @package SourceCode\Service
 */
class DataCollect
{
    protected $entityManager;

    protected $languageData;

    protected $codeCommands;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->languageData = null;
        $this->codeCommands = null;
    }

    /**
     * Retira os dados dos comandos de desvio do código
     *
     * @param SourceCode $sourceCode
     * @return null
     * @throws \Exception
     */
    public function getDataFromCode(SourceCode $sourceCode)
    {
        //todo lembrar de refatorar o código antes de chegar nesta função
        $language = $sourceCode->getLanguage();
        //todo verificar se esse explode está funcionando
        $codeContent = explode(PHP_EOL, $sourceCode->getContent());
        //indica se a linha contém texto
        $isText = false;
        //indica se a linha contém comentário
        $isComment = false;
        //indica se o código possui a estrutura inicial de código
        $removeLastKey = false;
        //todo verificar se essa opção será usada
        $removeKeys = false;
        //linha corrente analisada
        $lineNumber = 0;
        $previusCharacter = "";
        $previusToken = "";
        $token = "";
        $terminalDoWhile = null;
        $terminalSwitchCase = null;
        $lastCommandCase = null;
        $addToken = true;

        //contadores de variáveis, linhas úteis e operadores lógicos
        $countVariables = 0;
        $countLines = 0;
        $countLogicalConnectives = 0;

        //busca a e define a estrutura da linguagem do banco de dados
        $this->getLanguageData($language->getId());

//        print_r($this->languageData['specialCharacters']);
        //percorre as linhas do código
        foreach($codeContent as $line) {
            $lineNumber++;
            $isText = false;
            $isComment = false;
            //quebra a linha em um array de caracteres caracteres
            $characters = str_split($line);

            //todo verificar essa opção para identificação das variáveis
            //se a linha não contém nenhum comando de desvio marca texto e comentário
            if(!$isComment && !$this->lineContainsInitialBypassCommand($line) && !$this->lineContainsTerminalBypassCommand($line)) {
                $isText = true;
                $isComment = true;
            }

            //verifica se a linha contém a estrutura de início de código
            if($this->lineContainsStartCodeStructure($line, $language->getStartCodeStructure()) && !$removeLastKey) {
                $removeLastKey = true;
            }


             //Verifica se a linha não possui o token de terminal do comando do do-while, que deve ser ignorado na adição
            if(!$this->lineContainsToken($line, $terminalDoWhile)) {
                $addToken = true;
            } else if($this->lineContainsToken($line, ";")){
                $addToken = false;
            }

//            print_r($characters);

            //todo verifica as variáveis e comentários

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
                // Armazena o caracter lido para ser usado como anterior
                $previusCharacter = $character;

//                \Zend\Debug\Debug::dump('################');
//                \Zend\Debug\Debug::dump('token:'. $token .".");
//                \Zend\Debug\Debug::dump(array_slice($characters, 0, ($keyChar+1)));
//                \Zend\Debug\Debug::dump('character:'. $character.".");
//                \Zend\Debug\Debug::dump('!TEXT && !COMMENT:'. (string)(!$isComment && !$isText).".");
//
//                \Zend\Debug\Debug::dump('---- TOKENS -----');
//
//                $arrayResult = array();
//                foreach ($this->codeCommands as $value) {
//                    if($value instanceof CodeBypassCommand)
//                        $arrayResult[] = $value->getName();
//                }
//                \Zend\Debug\Debug::dump($arrayResult);
//                print_r("-------");

                // 3. - Se não é comentário e texto o caracter é parte do código efetivo.
                if(!$isComment && !$isText) {
                    $teste = ($this->isSpecialCharacter($character) || $character === " ");
//                    \Zend\Debug\Debug::dump('characterIF:'.  $teste . ".");
                    // 3.1 - Se o caracter for um espaço ou um caracter especial
                    if($this->isSpecialCharacter($character) || $character === " ") {
                        if($addToken) {
                            /* Para os casos da linguagem C,
                              que possui um caractere especial (}) como terminal de comando de desvio */
                            if ($this->isTerminalBypassCommand($character) && $token == "") {
//                                \Zend\Debug\Debug::dump(' $token = $character' . $character . ".");
                                $token = $character;
                            }

                            // 3.1.1 - Este caso é para tratar o ELSE IF
                            if ($this->isBypassCommandElse($previusToken) && $this->isBypassCommandIf($token))
                                //envia os tokens concatenados
                                $this->addToken($previusToken . $token, $lineNumber);
                            else {
                                $this->addToken($token, $lineNumber);
                            }

                            /* Verifica se o comando representado pelo token é um comando que representa o comando inicial
                              do-while na linguagem e se seu terminal é um comando também inicial na liguagem (caso da Linguagem C)
                              para definir uma exceção para esse comando não ser adicionado na lista de comandos de desvio do código
                              duas vezes
                             */
                            if($this->isBypassCommandDoWhile($token) && $this->terminalBypassCommandDoWhileIsInitial()) {
                                $terminalDoWhile = $this->getBypassCommandDoWhile()['terminalCommandName'];
                            }

                            //todo rever estas condições
                            if($this->isBypassCommandSwitch($token)) {
                                $terminalSwitchCase = $this->getCommandFromToken($token)['terminalCommandName'];
                            }
                            else if($terminalSwitchCase !== null && $this->isBypassCommandCaseOrDefault($token)) {
                                $lastCommandCase = $token;
                            }
                            //todo fazer função para comparar o token com o terminal do switch case
                            else if ($lastCommandCase !== null && isTerminalSwitchCase($token)) {
                                $this->addToken($terminalSwitchCase, $lineNumber);
                                $lastCommandCase = null;
                                $terminalSwitchCase = null;
                            }

                            // 3.1.2 - salva o token anterior somente se o caracter for um espaço e se o token estiver preenchido
                            if ($character === " " && !empty($token))
                                $previusToken = $token;
                            else if ($this->isSpecialCharacter($character)) // 3.1.3 - Se for um caracter especial o tokenAnt recebe vazio
                                $previusToken = "";

                            //todo verificar como fazer este comando
                            // 3.1.4 - Se for um abre ou fecha chaves adiciona-o na lista de comandos
//                        if($this->isTerminalBypassCommand()) {
//
//                        }
                        }
                        /*se addToken for falso significa que o terminal de comando desvio foi encontrado e não deve ser adicionado,
                          podendo ser inicializado novamente
                        */
                        else {
                            $terminalDoWhile = null;
                            $addToken = true;
                        }
                        $token = "";
                    } else
                        // Incrementa caracter por caracter lido a variável token
                        $token .= $character;
                }
            }
//            print_r(" | ");
        }

//        \Zend\Debug\Debug::dump("last key remove: ");
//        var_dump($removeLastKey);

        //verifica se a última chave deve ser removida (chave que indica o fechamento do código)
        if($removeLastKey && !empty($this->codeCommands))
            array_pop($this->codeCommands);

        // Será adicionado um ponto no final para atender a classe ctrlAnalisaEstrutura,
        // simbolizando o fim do vetor.
        $this->addToken(".", 0);

        //todo adicionar uma visualização do array

        return $this->codeCommands;
    }

    /**
     * Retorna os dados de uma linguagem de programação salva através de seu id
     * @param $languageId int
     * @throws \Exception
     */
    private function getLanguageData($languageId)
    {
        //monta a query para trazer todos os comandos de desvio da linguagem utilizada no código fonte
        $diversionCommands = $this->entityManager->createQueryBuilder()
            ->select('bc.id, bc.initialCommandName, bc.terminalCommandName, bc.type, graphElement.name as graphElementName')
            ->from(BypassCommand::class, 'bc')
            ->innerJoin('bc.languages', 'language')
            ->leftJoin('bc.graphElement', 'graphElement')
            ->where('language.id = :languageId')
            ->setParameter('languageId', $languageId);

        //retorna os comandos condicionais da linguagem utilizada no código fonte
        $conditionalCommands = clone $diversionCommands;
        $conditionalCommands = $conditionalCommands->andWhere("bc.type like :conditional")
        ->setParameter('conditional', BypassCommand::TYPE_CONDITIONAL)
        ->getQuery()
        ->getArrayResult();

        //retorna os comandos de repetição da linguagem utilizada no código fonte
        $loopCommands = clone $diversionCommands;
        $loopCommands = $loopCommands->andWhere("bc.type like :loop")
            ->setParameter('loop', BypassCommand::TYPE_LOOP)
            ->getQuery()
            ->getArrayResult();

        //retorna os conectivos lógicos da linguagem utilizada no código fonte
        $logicalConnectives = $this->entityManager->createQueryBuilder()
            ->select('lc.id, lc.name')
            ->from(LogicalConnective::class, 'lc')
            ->innerJoin('lc.languages', 'language')
            ->where('language = :languageId')
            ->setParameter('languageId', $languageId)
            ->getQuery()
            ->getArrayResult();
        $logicalConnectives = array_column($logicalConnectives, 'name');

        //retorna os tipos de dados da linguagem utilizada no código fonte
        $dataTypes = $this->entityManager->createQueryBuilder()
            ->select('dt.id, dt.name')
            ->from(DataType::class, 'dt')
            ->innerJoin('dt.languages', 'language')
            ->where('language = :languageId')
            ->setParameter('languageId', $languageId)
            ->getQuery()
            ->getArrayResult();
        $dataTypes = array_column($dataTypes, 'name');

        //retorna os tipos de dados da linguagem utilizada no código fonte
        $specialCharacters = $this->entityManager->createQueryBuilder()
            ->select('sc.id, sc.name')
            ->from(SpecialCharacter::class, 'sc')
            ->innerJoin('sc.languages', 'language')
            ->where('language = :languageId')
            ->setParameter('languageId', $languageId)
            ->getQuery()
            ->getArrayResult();
        $specialCharacters = array_column($specialCharacters, 'name');

        if(count($conditionalCommands) < 1 || count($loopCommands) < 1 || count($logicalConnectives) < 1 || count($dataTypes) < 1 || count($specialCharacters) < 1) {
            throw new \Exception('Erro ao carregar os dados da Linguagem de Programação');
        }

        $this->languageData = array(
            'diversionCommands' => array_merge($conditionalCommands, $loopCommands),
            'conditionalCommands' => $conditionalCommands,
            'loopCommands' => $loopCommands,
            'logicalConnectives' => $logicalConnectives,
            'dataTypes' => $dataTypes,
            'specialCharacters' => $specialCharacters,
        );
    }

    /**
     * Informa se um token é um comando de desvio da Linguagem de Programação ou não
     *
     * @param $token
     * @return bool
     */
    private function isInitialBypassCommand($token)
    {
        //transforma o token para minúsculas
        $token = strtolower($token);
        return in_array($token, array_column($this->languageData['diversionCommands'], 'initialCommandName'));
    }

    /**
     * Informa se uma linha contém comando de desvio
     *
     * @param $line
     * @return bool
     */
    private function lineContainsInitialBypassCommand($line)
    {
        //transforma o token para minúsculas
        $line = strtolower($line);
        foreach ($this->languageData['diversionCommands'] as $bypassCommand) {
            if(strpos($line, $bypassCommand['initialCommandName']) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Informa se uma linha contém comando de desvio
     *
     * @param $line
     * @return bool
     */
    private function lineContainsTerminalBypassCommand($line)
    {
        //transforma a linha para minúsculas
        $line = strtolower($line);
        foreach ($this->languageData['diversionCommands'] as $bypassCommand) {
            if(strpos($line, $bypassCommand['terminalCommandName']) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifica se a linha contém a estrutura inicial do código
     *
     * @param $line
     * @param $startCodeStructures
     * @return bool
     */
    private function lineContainsStartCodeStructure($line, $startCodeStructures)
    {
        $startCodeStructures = explode("|", $startCodeStructures);
        foreach ($startCodeStructures as $startCodeStructure) {
            if(strpos($line, $startCodeStructure) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifica se uma determinada linha contém um determinado token
     *
     * @param $line
     * @param $token
     * @return bool
     */
    private function lineContainsToken($line, $token)
    {
        //transforma o token e a linha para minúsculas
        $line = strtolower($line);
        $token = strtolower($token);
        if(strpos($line, $token) !== false) {
            return true;
        }

        return false;
    }

    /**
     * Informa se o token representa o comando de desvio "IF"
     * @param $token
     * @return bool
     */
    private function isBypassCommandIf($token)
    {
        //obtém apenas os elementos gráficos dos comandos de desvio condicionais da linguagem
        $graphElements = array_column($this->languageData['conditionalCommands'], 'graphElementName');
        //identifica o índice do elemento gráfico que representa o if nos comandos de desvio através do índice do elemento gráfico
        $indexOfElement = array_search("if", $graphElements);
        //obtém o nome do comando de desvio inicial que representa o if na linguagem
        $bypassCommandIf = $this->languageData['conditionalCommands'][$indexOfElement]['initialCommandName'];
        return $token === $bypassCommandIf;

    }

    /**
     *  Informa se o token representa o comando de desvio "ELSE"
     *
     * @param $token
     * @return bool
     */
    private function isBypassCommandElse($token)
    {
        //obtém apenas os elementos gráficos dos comandos de desvio condicionais da linguagem
        $graphElements = array_column($this->languageData['conditionalCommands'], 'graphElementName');
        //identifica o índice do elemento gráfico que representa o else nos comandos de desvio através do índice do elemento gráfico
        $indexOfElement = array_search("if-else", $graphElements);
        //obtém o nome do comando de desvio inicial que representa o else na linguagem
        $bypassCommandElse = $this->languageData['conditionalCommands'][$indexOfElement]['initialCommandName'];
        return $token === $bypassCommandElse;

    }

    /**
     * Retorna todos os dados do comando de desvio do-while
     *
     * @return mixed
     */
    private function getBypassCommandDoWhile()
    {
        //obtém apenas os elementos gráficos dos comandos de desvio de repetição da linguagem
        $graphElements = array_column($this->languageData['loopCommands'], 'graphElementName');
        //identifica o índice do elemento gráfico que representa o do-while nos comandos de desvio através do índice do elemento gráfico
        $indexOfElement = array_search("do-while", $graphElements);
        //retorna o comando de desvio que representa o do-while na linguagem
        return $this->languageData['loopCommands'][$indexOfElement];
    }

    /**
     * Informa se o token é representa o comando desvio com elemento do grafo representando o do-while
     * @param $token
     * @return bool
     */
    private function isBypassCommandDoWhile($token)
    {
        //obtém o nome do comando de desvio inicial que representa o do-while na linguagem
        $initialBypassCommandDoWhile = $this->getBypassCommandDoWhile()['initialCommandName'];
        return $token === $initialBypassCommandDoWhile;
    }

    /**
     * Verifica se o terminal do comando de desvio do-while também é um inicial de comando de desvio
     * @return bool
     */
    private function terminalBypassCommandDoWhileIsInitial()
    {
        //obtém o nome do terminal do comando de desvio do-while na linguagem
        $terminalBypassCommandDoWhile = $this->getBypassCommandDoWhile()['terminalCommandName'];
        return $this->isInitialBypassCommand($terminalBypassCommandDoWhile);
    }

    /**
     * Retorna todos os dados do comando de desvio de acordo com o token enviado
     *
     * @param $token
     * @return mixed
     */
    private function getCommandFromToken($token)
    {
        //obtém apenas os nomes iniciais dos comandos de desvio condicionais da linguagem
        $commandNames = array_column($this->languageData['diversionCommands'], 'initialCommandName');
        //identifica o índice do comando que representa o token nos comandos de desvio
        $indexOfElement = array_search($token, $commandNames);
        //retorna o comando de desvio que representa o token na linguagem
        return $this->languageData['diversionCommands'][$indexOfElement];
    }

    /**
     * Informa se o token é um comando de desvio switch
     *
     * @param $token
     * @return mixed
     */
    private function isBypassCommandSwitch($token)
    {
        $commandSwitchCase = array();
        //obtém apenas os elementos gráficos dos comandos de desvio condicionais da linguagem
        $graphElements = array_column($this->languageData['conditionalCommands'], 'graphElementName');
        //identifica o índice dos elementos gráficos que representam o switch-case nos comandos de desvio através dos índices dos elementos gráficos
        $indexOfElements = array_keys($graphElements, "switch-case");
        //percorre os indíces para encontrar o comando que representa o switch
        foreach ($indexOfElements as $indexOfElement) {
            //transforma a string de terminais de comando em array
            $terminalCommandsLength =  count(explode("|", $this->languageData['conditionalCommands'][$indexOfElement]['terminalCommandName']));
            //ignora os comandos que possuem mais de um terminal porque representam o case e o default
            if($terminalCommandsLength < 2) {
                $commandSwitchCase = $this->languageData['conditionalCommands'][$indexOfElement];
                break;
            }
        }
        //retorna se o token enviado é o comando switch na linguagem
        return count($commandSwitchCase) > 0 && $token === $commandSwitchCase['initialCommandName'];
    }

    /**
     * Informa se o token é um comando de desvio case ou default
     *
     * @param $token
     * @return bool
     */
    private function isBypassCommandCaseOrDefault($token)
    {
        $isCaseOrDefault = false;
        //obtém apenas os elementos gráficos dos comandos de desvio condicionais da linguagem
        $graphElements = array_column($this->languageData['conditionalCommands'], 'graphElementName');
        //identifica o índice dos elementos gráficos que representam o switch-case nos comandos de desvio através dos índices dos elementos gráficos
        $indexOfElements = array_keys($graphElements, "switch-case");
        //percorre os indíces para encontrar o comando que representa o switch
        foreach ($indexOfElements as $indexOfElement) {
            //transforma a string de terminais de comando em array
            $terminalCommandsLength =  count(explode("|", $this->languageData['conditionalCommands'][$indexOfElement]['terminalCommandName']));
            //ignora o comando "switch" que possui apenas um terminal
            if($terminalCommandsLength > 1) {
                $command = $this->languageData['conditionalCommands'][$indexOfElement];
                //compara o nome do token com o comando
                if($command['initialCommandName'] === $token) {
                    $isCaseOrDefault = true;
                    break;
                }
            }
        }
        //retorna se o token enviado é o comando switch na linguagem
        return $isCaseOrDefault;
    }

    /**
     * Informa se o token é um comando de desvio da Linguagem de Programação ou não
     *
     * @param $token
     * @return bool
     */
    private function isTerminalBypassCommand($token)
    {
        //transforma o token para minúsculas
        $token = strtolower($token);
        foreach ($this->languageData['diversionCommands'] as $bypassCommand) {
            $terminalCommands = explode("|", $bypassCommand['terminalCommandName']);
            if(in_array($token, $terminalCommands)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifica se um determinado token é o terminal de comando do último comando de desvio adicionado a lista de comandos do código
     *
     * @param $previusBypassCommand
     * @param $token
     * @return bool
     */
    private function isTerminalBypassCommandLastCodeCommand($previusBypassCommand, $token)
    {
        $terminalCommands = array();

        if($previusBypassCommand instanceof CodeBypassCommand) {
            //obtém apenas os nomes iniciais dos comandos de desvio da linguagem
            $initialCommandNames = array_column($this->languageData['diversionCommands'], 'initialCommandName');
            /*identifica o índice do comando de desvio que representa
             o $previusBypassCommand nos comandos de desvio através de seu nome */
            $indexOfElement = array_search($previusBypassCommand->getName(), $initialCommandNames);
            //retorna o terminal do comando de desvio que representa o $previusBypassCommand na linguagem
            $terminalCommands = explode("|", $this->languageData['diversionCommands'][$indexOfElement]['terminalCommandName']);
        }
        return in_array($token, $terminalCommands);
    }

    /**
     * Informa se o caractere é um caractere especial da Linguagem de Programação
     *
     * @param $character
     * @return bool
     */
    private function isSpecialCharacter($character)
    {
        //transforma o token para minúsculas
        $character = strtolower($character);
        return in_array($character, $this->languageData['specialCharacters']);
    }

    /**
     * Adiciona o token na lista de comandos de desvio do código
     * @param $token
     * @param $lineNumber
     */
    private function addToken($token, $lineNumber)
    {
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
        if($this->isTerminalBypassCommand($token) && $lastCommand instanceof CodeBypassCommand && $lastCommand->getName() === "{") {
            if($this->isTerminalBypassCommandLastCodeCommand($lastButOne, $token)) {
                //salva a linha inicial do último comando
                if ($lastCommand instanceof CodeBypassCommand)
                    $initialLineNumber = $lastCommand->getInitialLineNumber();

                //remove o último elemento da lista de comandos que seria uma chave "{"
                array_pop($this->codeCommands);

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
        else if($this->isTerminalBypassCommand($token) && $lastCommand instanceof CodeBypassCommand && $lastCommand->getName() !== "{") {
            //cria o comando fechamento de bloco
            $endBlockCommand = new CodeBypassCommand();
            $endBlockCommand->setName("}");
            $endBlockCommand->setInitialLineNumber($lineNumber);

            array_push($this->codeCommands, $endBlockCommand);
        }

        // Se o token lido for um Comando início de desvio então deve ser armazenado na lista de comandos.
        if($this->isInitialBypassCommand($token)) {
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
        } else if($token === ".") { //verifica se é o caractere que indica o final da lista de comandos
            //cria o indicador de final de comandos
            $endCodeBypassCommand = new CodeBypassCommand();
            $endCodeBypassCommand->setName($token);
            $endCodeBypassCommand->setInitialLineNumber($lineNumber);
            array_push($this->codeCommands, $endCodeBypassCommand);
        }
    }

}