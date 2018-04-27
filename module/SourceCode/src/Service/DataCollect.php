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
//                \Zend\Debug\Debug::dump('!TEXT && !COMMENT:'. !$isComment && !$isText.".");

//                \Zend\Debug\Debug::dump('---- TOKENS -----');

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
                    //var_dump('    character: ' .$character.'     ');
                    // 3.1 - Se o caracter for um espaço ou um caracter especial
                    if($this->isSpecialCharacter($character) || $character === " ") {


                        /* Para os casos da linguagem C,
                          que possui um caractere especial (}) como terminal de comando de desvio */
                        if($this->isTerminalBypassCommand($character) && $token == "") {
//                            \Zend\Debug\Debug::dump(' $token = $character'.  $character . ".");
                            $token = $character;
                        }

                        // 3.1.1 - Este caso é para tratar o ELSE IF
                        if ($this->isBypassCommandElse($previusToken) && $this->isBypassCommandIf($token))
                            //envia os tokens concatenados
                            $this->addToken($previusToken.$token, $lineNumber);
                        else
                            $this->addToken($token, $lineNumber);

                        // 3.1.2 - salva o token anterior somente se o caracter for um espaço e se o token estiver preenchido
                        if($character === " " && !empty($token))
                            $previusToken = $token;
                        else if($this->isSpecialCharacter($character)) // 3.1.3 - Se for um caracter especial o tokenAnt recebe vazio
                            $previusToken = "";

                        //todo verificar como fazer este comando
                        // 3.1.4 - Se for um abre ou fecha chaves adiciona-o na lista de comandos
//                        if($this->isTerminalBypassCommand()) {
//
//                        }

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
        //transforma o token para minúsculas
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

//        \Zend\Debug\Debug::dump('########################################');
//
//        $arrayResult = array();
//        foreach ($this->codeCommands as $value) {
//            if($value instanceof CodeBypassCommand)
//                $arrayResult[] = $value->toArray();
//        }
//        \Zend\Debug\Debug::dump($arrayResult);
//        print_r("-------");


        end($this->codeCommands);
//        //$lengthCommands = count($this->codeCommands);
//        //$lastIndex = ($lengthCommands - 1);
        $lastIndex = key($this->codeCommands);
        $lastCommand = &$this->codeCommands[$lastIndex]; //retorno por referência

        //print_r('length commands ' . $lengthCommands);
//        \Zend\Debug\Debug::dump('token: '. $token);
//        print_r("-------");

//        \Zend\Debug\Debug::dump('bypass '. $lastIndex .' instance of: ');
//        var_dump($lastCommand instanceof CodeBypassCommand);
//        var_dump("-------");

//        \Zend\Debug\Debug::dump('condition p/ remove { : ');
//        var_dump(($this->isTerminalBypassCommand($token) && $lastCommand instanceof CodeBypassCommand && $lastCommand->getName() === "{"));
//        print_r("-------");

//        \Zend\Debug\Debug::dump('is TERMINAL : ');
//        var_dump($this->isTerminalBypassCommand($token));
//        print_r("-------");


        /* - Se o token for um token de término de comando de desvio e o último da lista for um {, então remove da lista o {
           para que permaneçam na lista de vértices apenas as chaves que tiverem comandos aninhados
        */
        if($this->isTerminalBypassCommand($token) && $lastCommand instanceof CodeBypassCommand && $lastCommand->getName() === "{") {
            //salva a linha inicial do último comando
            if($lastCommand instanceof CodeBypassCommand)
                $initialLineNumber = $lastCommand->getInitialLineNumber();

            //remove o último elemento da lista de comandos que seria uma chave "{"
            array_pop($this->codeCommands);

//            $arrayResult = array();
//            foreach ($this->codeCommands as $value) {
//                if($value instanceof CodeBypassCommand)
//                    $arrayResult[] = $value->toArray();
//            }
//            print_r($arrayResult);
//            print_r("|                 proximo            | ");

            //recalcula os tamanhos e adquire o novo último comando da lista de comandos de desvio do código
            end($this->codeCommands);
            $lastIndex = key($this->codeCommands);
            $lastCommand = &$this->codeCommands[$lastIndex]; //retorno por referência

//            print_r($lastCommand->getName());

            //define a linha inicial do comando para a linha inicial do comando "{" que foi removido
            if($lastCommand instanceof CodeBypassCommand) {
                $lastCommand->setInitialLineNumber($initialLineNumber);
                $lastCommand->setEndLineNumber($lineNumber);
            }
            //todo modifica esse if para isTerminal && isInitial && $lastCommand['graph_element'] = do-while (colocar em uma função se necessário)
        } else if(($this->isTerminalBypassCommand($token) && !$this->isInitialBypassCommand($token)) && $lastCommand instanceof CodeBypassCommand && $lastCommand->getName() !== "{") { //se token for um término de comando de desvio e o anterior for diferente de "{" adiciona o fechamento de bloco
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