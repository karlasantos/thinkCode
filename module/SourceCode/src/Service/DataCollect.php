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

        //busca a estrutura da linguagem do banco de dados
        $this->getLanguageData($language->getId());

        //percorre as linhas do código
        foreach($codeContent as $line) {
            $lineNumber++;
            $isText = false;
            $isComment = false;
            //quebra a linha em um array de caracteres caracteres
            $characters = str_split($line);

            //todo verificar essa opção para identificação das variáveis
            //se a linha não contém nenhum comando de desvio marca texto e comentário comentário
            if(!$isComment && !$this->lineContainsBypassCommand($line)) {
                $isText = true;
                $isComment = true;
            }

            //verifica se a linha contém a estrutura de início de código
            if($this->lineContainsStartCodeStructure($line, $language->getStartCodeStructure())) {
                $removeLastKey = true;
            }

            //todo verifica as variáveis e comentários

            //Percorre os caracteres de cada linha
            foreach ($characters as $character) {

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

                // 3. - Se não é comentário e texto o caracter é parte do código efetivo.
                if(!$isComment && !$isText) {
                    // 3.1 - Se o caracter for um espaço ou um caracter especial
                    if($this->isSpecialCharacter($character) || $character === " ") {
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
        }

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
            ->select('bc.id, bc.initialCommandName, bc.terminalCommandName, bc.type, graphElement.name as graphElement')
            ->from(BypassCommand::class, 'bc')
            ->innerJoin('bc.languages', 'language')
            ->leftJoin('bc.graphElement', 'graphElement')
            ->where('language.id = :languageId')
            ->setParameter('languageId', $languageId);

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
    private function lineContainsBypassCommand($line)
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
        $graphElements = array_column($this->languageData['conditionalCommands'], 'graphElement');
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
        $graphElements = array_column($this->languageData['conditionalCommands'], 'graphElement');
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
            if(strpos($bypassCommand['terminalCommandName'], $token) !== false) {
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
        //cria a estrutura do comando de desvio do código
        $codeByspassCommand = array(
            'name' => null,
            'indexReferentNode' => null, // posição do comando na lista de vértices
            'openingIndex' => null, //posição do comando reponsável pela abertura de bloco: "{"
            'initialLineNumber' => null, //número da linha de início de comando
            'endLineNumber' => null, //número da linha de final de comando
        );

        $lengthCommands = count($this->codeCommands);
        $lastIndex = $lengthCommands - 1;
        $lastCommand = &$this->codeCommands[$lastIndex]; //retorno por referência

        /* - Se o token for um token de término de comando de desvio e o último da lista for um {, então remove da lista o {
           para que permaneçam na lista de vértices apenas as chaves que tiverem comandos aninhados
        */
        if($this->isTerminalBypassCommand($token) && $lastCommand['name'] === "{") {
            //salva a linha inicial do último comando
            $initialLineNumber = $lastCommand['initialLineNumber'];
            $endLineNumber = $lineNumber;

            //remove o último elemento da lista de comandos que seria uma chave "{"
            array_pop($this->codeCommands);

            //recalcula os tamanhos e adquire o novo último comando da lista de comandos de desvio do código
            $lengthCommands = count($this->codeCommands);
            $lastIndex = $lengthCommands - 1;
            $lastCommand = &$this->codeCommands[$lastIndex]; //retorno por referência

            //define a linha inicial do comando para a linha inicial do comando "{" que foi removido
            $lastCommand['initialLineNumber'] = $initialLineNumber;
            $lastCommand['endLineNumber'] = $endLineNumber;
        } else if($this->isTerminalBypassCommand($token) && $lastCommand['name'] !== "{") { //se token for um término de comando de desvio e o anterior for diferente de "{" adiciona o fechamento de bloco
            //cria o comando fechamento de bloco
            $codeByspassCommand['name'] = "}";
            $codeByspassCommand['initialLineNumber'] = $lineNumber;

            array_push($this->codeCommands, $codeByspassCommand);
        }

        //cria o comando de desvio
        $codeByspassCommand['name'] = $token;
        $codeByspassCommand['initialLineNumber'] = $lineNumber;

        // Se o token lido for um Comando início de desvio então deve ser armazenado na lista de comandos.
        if($this->isInitialBypassCommand($token)) {
            //cria o comando de início de bloco
            $initialBlockCommand = $codeByspassCommand;
            $initialBlockCommand['name'] = "{";

            //adiciona o comando de desvio e o comando de início de bloco nos últimos índices do array de comandos de desvio do código
            array_push($this->codeCommands, $codeByspassCommand, $initialBlockCommand);
        } else if($token === ".") { //verifica se é o caractere que indica o final da lista de comandos
            array_push($this->codeCommands, $codeByspassCommand);
        }
    }

}