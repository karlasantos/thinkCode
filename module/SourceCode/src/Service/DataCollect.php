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

    public function getDataFromCode(SourceCode $sourceCode)
    {
        //todo lembrar de refatorar o código antes de chegar nesta função

        $language = $sourceCode->getLanguage();
        //todo verificar se esse explode está funcionando
        $codeContent = explode(PHP_EOL, $sourceCode->getContent());
        $isText = false;
        $isComment = false;
        $removeLastKey = false;
        $removeKeys = false;
        $lineNumber = 0;
        $previusCharacter = "";
        $previusToken = "";

        $this->languageData = $this->getLanguageData($language->getId());

        foreach($codeContent as $line) {
            $lineNumber++;
            $isText = false;
            $isComment = false;
            //quebra a linha em um array de caracteres caracteres
            $characters = str_split($line);

            foreach ($characters as $character) {
                if($isComment === false && $character === '"')
                    $isText = true;

                //TODO PAREI AQUI
                if($isComment === false) {

                }
            }

        }

        //todo transformar o codigo em lowercase

        return $this->languageData;

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
            ->select('bc.id, bc.initialCommandName, bc.terminalCommandName, bc.type')
            ->from(BypassCommand::class, 'bc')
            ->innerJoin('bc.languages', 'language')
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
     * Informa se um token é um comando de desvio da Linguagem de Programação ou não
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
     * Informa se um caractere é um caractere especial da Linguagem de Programação
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
     * Adiciona um token na lista de comandos de desvio do código
     * @param $token
     * @param $lineNumber
     */
    private function addToken($token, $lineNumber)
    {
        //cria a estrutura do comando de desvio do código
        $codeByspassCommand = [
            'name' => null,
            'indexReferentNode' => null, // posição do comando na lista de vértices
            'openingIndex' => null, //posição do comando reponsável pela abertura de bloco: "{"
            'initialLineNumber' => null, //número da linha de início de comando
            'endLineNumber' => null, //número da linha de final de comando
        ];

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

        // Se o token lido for um Comando início de desvio então deve ser armazenado na lista de comandos.
        if($this->isInitialBypassCommand($token)) {
            //cria o comando de desvio
            $codeByspassCommand['name'] = $token;
            $codeByspassCommand['initialLineNumber'] = $lineNumber;

            //cria o comando de início de bloco
            $initialBlockCommand = $codeByspassCommand;
            $initialBlockCommand['name'] = "{";

            //adiciona o comando de desvio e o comando de início de bloco nos últimos índices do array de comandos de desvio do código
            array_push($this->codeCommands, $codeByspassCommand, $initialBlockCommand);
        }
    }

}