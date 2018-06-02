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
use SourceCode\Model\Entity\SpecialCharacter;
use SourceCode\Model\Entity\Language as LanguageEntity;

/**
 * Class Language
 * Serviço responsável pelo carregamento e verificação dos dados da linguagem
 * @package SourceCode\Service
 */
class Language
{
    /**
     * Gerenciador de entidades do Doctrine
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Elementos da linguagem de programação (Tipos de Dados, Caracteres Especiais, Conectivos Lógicos e Comandos de Desvio) em formato de array
     *
     * @var array
     */
    private $elementsOfLanguage;

    /**
     * DataCollect constructor.
     * Inicializa todas as variáveis do service Language
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->elementsOfLanguage  = null;
    }

    /**
     * Retorna os elementos de uma linguagem de programação através de seu id
     *
     * @param $languageId
     * @return array
     * @throws \Exception
     */
    public function searchElementsOfLanguage($languageId)
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

        $language = $this->entityManager->find(LanguageEntity::class, $languageId);

        if(count($conditionalCommands) < 1 || count($loopCommands) < 1 || count($logicalConnectives) < 1 || count($dataTypes) < 1 || count($specialCharacters) < 1 || !$language instanceof LanguageEntity) {
            throw new \Exception('Erro ao carregar os dados da Linguagem de Programação');
        }

        $this->elementsOfLanguage = array(
            'diversionCommands' => array_merge($conditionalCommands, $loopCommands),
            'conditionalCommands' => $conditionalCommands,
            'loopCommands' => $loopCommands,
            'logicalConnectives' => $logicalConnectives,
            'dataTypes' => $dataTypes,
            'specialCharacters' => $specialCharacters,
            'initialCodeStructure' => $language->getInitialCodeStructure(),
            'endCodeStructure' => $language->getEndCodeStructure(),
        );

        return $this->elementsOfLanguage;
    }

    /**
     * Retorna os elementos da linguagem de programação (Tipos de Dados, Caracteres Especiais, Conectivos Lógicos e Comandos de Desvio)
     *
     * @return array
     */
    public function getElementsOfLanguage()
    {
        return $this->elementsOfLanguage;
    }

    /**
     * Informa se um token é um comando de desvio da Linguagem de Programação ou não
     *
     * @param string $token
     * @return bool
     */
    public function isInitialBypassCommand($token)
    {
        //transforma o token para minúsculas
        $token = strtolower($token);
        return in_array($token, array_column($this->elementsOfLanguage['diversionCommands'], 'initialCommandName'));
    }

    /**
     * Retorna o index de um comando de desvio condicional através do nome de seu elemento gráfico
     *
     * @param string $graphElementName
     * @return array
     */
    private function indexOfConditionalBypassCommand($graphElementName)
    {
        //obtém apenas os elementos gráficos dos comandos de desvio de repetição da linguagem
        $graphElements = array_column($this->elementsOfLanguage['conditionalCommands'], 'graphElementName');
        /* identifica o índice do elemento gráfico que representa
          o nome do elemento nos comandos de desvio através do índice do elemento gráfico */
        return array_keys($graphElements, $graphElementName);
    }

    /**
     * Retorna o index de um comando de desvio de repetição através do nome de seu elemento gráfico
     *
     * @param string $graphElementName
     * @return false|int|string
     */
    private function indexOfLoopBypassCommand($graphElementName)
    {
        //obtém apenas os elementos gráficos dos comandos de desvio de repetição da linguagem
        $graphElements = array_column($this->elementsOfLanguage['loopCommands'], 'graphElementName');
        //identifica o índice do elemento gráfico que representa o do-while nos comandos de desvio através do índice do elemento gráfico
        return array_search($graphElementName, $graphElements);
    }

    /**
     * Retorna o comando de desvio if da linguagem em formato de array
     * @return array
     */
    public function getBypassCommandIf()
    {
        //identifica o índice do comando de desvio que representa o if
        $indexOfElement = $this->indexOfConditionalBypassCommand("if")[0];
        //obtém o nome do comando de desvio inicial que representa o if na linguagem
        return $this->elementsOfLanguage['conditionalCommands'][$indexOfElement];
    }

    /**
     * Informa se o token representa o comando de desvio "IF"
     * @param string $token
     * @return bool
     */
    public function isInitialBypassCommandIf($token)
    {
        $bypassCommandIf = $this->getBypassCommandIf()['initialCommandName'];
        return $token === $bypassCommandIf;

    }

    /**
     * Retorna o comando de desvio else da linguagem em formato de array
     *
     * @return array
     */
    public function getBypassCommandElse()
    {
        //identifica o índice do elemento gráfico que representa o else nos comandos de desvio
        $indexOfElement = $this->indexOfConditionalBypassCommand("if-else")[0];
        //obtém o nome do comando de desvio inicial que representa o else na linguagem
        return $this->elementsOfLanguage['conditionalCommands'][$indexOfElement];
    }

    /**
     *  Informa se o token representa o comando de desvio "ELSE"
     *
     * @param string $token
     * @return bool
     */
    public function isInitialBypassCommandElse($token)
    {
        $bypassCommandElse = $this->getBypassCommandElse()['initialCommandName'];
        return $token === $bypassCommandElse;

    }

    /**
     * Retorna o nome inicial do comando de desvio else if
     *
     * @return string
     */
    public function getInitialBypassCommandElseIf()
    {
        $bypassCommandElse = $this->getBypassCommandElse()['initialCommandName'];
        $bypassCommandIf = $this->getBypassCommandIf()['initialCommandName'];
        return ($bypassCommandElse . $bypassCommandIf);
    }

    /**
     * Verifica se determinado token é o comando de desvio elfseif (junção de comandos else seguido de if)
     *
     * @param string $token
     * @return bool
     */
    public function isInitialBypassCommandElseIf($token)
    {
        $bypassCommandElseIf = $this->getInitialBypassCommandElseIf();
        return $token === $bypassCommandElseIf;
    }

    /**
     * Retorna todos os dados do comando de desvio do-while da linguagem
     *
     * @return array
     */
    public function getBypassCommandDoWhile()
    {
        //adquire o índice do elemento do-while
        $indexOfElement = $this->indexOfLoopBypassCommand("do-while");
        //retorna o comando de desvio que representa o do-while na linguagem
        return $this->elementsOfLanguage['loopCommands'][$indexOfElement];
    }

    /**
     * Informa se o token é representa o comando desvio com elemento do grafo representando o do-while
     * @param string $token
     * @return bool
     */
    public function isInitialBypassCommandDoWhile($token)
    {
        //obtém o nome do comando de desvio inicial que representa o do-while na linguagem
        $initialBypassCommandDoWhile = $this->getBypassCommandDoWhile()['initialCommandName'];
        return $token === $initialBypassCommandDoWhile;
    }

    /**
     * Informa se o token é um comando de desvio for
     *
     * @param string $token
     * @return bool
     */
    public function isInitialBypassCommandFor($token)
    {
        //adquire o índice do elemento do-while
        $indexOfElement = $this->indexOfLoopBypassCommand("for");
        //retorna o comando de desvio que representa o do-while na linguagem
        return $token === $this->elementsOfLanguage['loopCommands'][$indexOfElement]['initialCommandName'];
    }

    /**
     * Informa se o token é um comando de desvio while
     *
     * @param string $token
     * @return bool
     */
    public function isInitialBypassCommandWhile($token)
    {
        //adquire o índice do elemento do-while
        $indexOfElement = $this->indexOfLoopBypassCommand("while");
        //retorna o comando de desvio que representa o do-while na linguagem
        return $token === $this->elementsOfLanguage['loopCommands'][$indexOfElement]['initialCommandName'];
    }

    /**
     * Verifica se o terminal do comando de desvio do-while também é um inicial de comando de desvio
     * @return bool
     */
    public function terminalBypassCommandDoWhileIsAlsoInitial()
    {
        //obtém o nome do terminal do comando de desvio do-while na linguagem
        $terminalBypassCommandDoWhile = $this->getBypassCommandDoWhile()['terminalCommandName'];
        return $this->isInitialBypassCommand($terminalBypassCommandDoWhile);
    }

    /**
     * Retorna o comando de desvio switch case
     * @return array
     */
    public function getBypassCommandSwitch()
    {
        $commandSwitchCase = array();
        //identifica o índice dos elementos gráficos que representam o switch-case nos comandos de desvio através dos índices dos elementos gráficos
        $indexOfElements = $this->indexOfConditionalBypassCommand("switch-case");
        //percorre os indíces para encontrar o comando que representa o switch
        foreach ($indexOfElements as $indexOfElement) {
            //transforma a string de terminais de comando em array
            $terminalCommandsLength =  count(explode("|", $this->elementsOfLanguage['conditionalCommands'][$indexOfElement]['terminalCommandName']));
            //ignora os comandos que possuem mais de um terminal porque representam o case e o default
            if($terminalCommandsLength < 2) {
                $commandSwitchCase = $this->elementsOfLanguage['conditionalCommands'][$indexOfElement];
                break;
            }
        }
        return $commandSwitchCase;
    }

    /**
     * Informa se o token é um comando de desvio switch
     *
     * @param string $token
     * @return mixed
     */
    public function isInitialBypassCommandSwitch($token)
    {
        $commandSwitchCase = $this->getBypassCommandSwitch();
        //retorna se o token enviado é o comando switch na linguagem
        return count($commandSwitchCase) > 0 && $token === $commandSwitchCase['initialCommandName'];
    }

    /**
     * Retorna os iniciais de comandos case e default da linguagem
     *
     * @return array
     */
    public function getInitialBypassCommandsCaseAndDefault()
    {
        $commandNames = array();
        //identifica o índice dos elementos gráficos que representam o switch-case nos comandos de desvio através dos índices dos elementos gráficos
        $indexOfElements = $this->indexOfConditionalBypassCommand("switch-case");
        //percorre os indíces para encontrar o comando que representa o switch
        foreach ($indexOfElements as $indexOfElement) {
            //transforma a string de terminais de comando em array
            $terminalCommandsLength =  count(explode("|", $this->elementsOfLanguage['conditionalCommands'][$indexOfElement]['terminalCommandName']));
            //ignora o comando "switch" que possui apenas um terminal
            if($terminalCommandsLength > 1) {
                $commandNames[] = $this->elementsOfLanguage['conditionalCommands'][$indexOfElement]['initialCommandName'];
            }
        }
        return $commandNames;
    }

    /**
     * Informa se o token é um comando de desvio case ou default
     *
     * @param string $token
     * @return bool
     */
    public function isInitialBypassCommandCaseOrDefault($token)
    {
        //identifica o índice dos elementos gráficos que representam o switch-case nos comandos de desvio através dos índices dos elementos gráficos
        $indexOfElements = $this->indexOfConditionalBypassCommand("switch-case");
        //percorre os indíces para encontrar o comando que representa o case e default
        foreach ($indexOfElements as $indexOfElement) {
            //transforma a string de terminais de comando em array
            $terminalCommandsLength =  count(explode("|", $this->elementsOfLanguage['conditionalCommands'][$indexOfElement]['terminalCommandName']));
            //ignora o comando "switch" que possui apenas um terminal
            if($terminalCommandsLength > 1) {
                $command = $this->elementsOfLanguage['conditionalCommands'][$indexOfElement];
                //compara o nome do token com o comando
                if($command['initialCommandName'] === $token) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Retorna se um token é um comando de desvio Default
     *
     * @param string $token
     * @return bool
     */
    public function isInitialBypassCommandDefault($token)
    {
        $commandName = null;
        //identifica o índice dos elementos gráficos que representam o switch-case nos comandos de desvio através dos índices dos elementos gráficos
        $indexOfElements = $this->indexOfConditionalBypassCommand("switch-case");
        //percorre os indíces para encontrar o comando que representa o default
        foreach ($indexOfElements as $indexOfElement) {
            //transforma a string de terminais de comando em array
            $terminalCommandsLength =  count(explode("|", $this->elementsOfLanguage['conditionalCommands'][$indexOfElement]['terminalCommandName']));
            //ignora o comando "switch" que possui apenas um terminal
            if($terminalCommandsLength == 2) {
                $commandName = $this->elementsOfLanguage['conditionalCommands'][$indexOfElement]['initialCommandName'];
                break;
            }
        }

        return $token == $commandName;
    }

    /**
     * Retorna se o token é o terminal de comando do switch
     *
     * @param string $token
     * @return bool
     */
    public function isTerminalBypassCommandSwitch($token)
    {
        $commandSwitch = $this->getBypassCommandSwitch();
        return $token === $commandSwitch['terminalCommandName'];
    }

    /**
     * Informa se o token é um comando de desvio da Linguagem de Programação ou não
     *
     * @param string $token
     * @return bool
     */
    public function isTerminalBypassCommand($token)
    {
        //transforma o token para minúsculas
        $token = strtolower($token);
        foreach ($this->elementsOfLanguage['diversionCommands'] as $bypassCommand) {
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
     * @param string $character
     * @return bool
     */
    public function isSpecialCharacter($character)
    {
        //transforma o token para minúsculas
        $character = strtolower($character);
        return in_array($character, $this->elementsOfLanguage['specialCharacters']);
    }

    /**
     * Retorna todos os dados do comando de desvio for da linguagem
     *
     * @return array
     */
    public function getBypassCommandFor()
    {
        //adquire o índice do elemento for
        $indexOfElement = $this->indexOfLoopBypassCommand("for");
        //retorna o comando de desvio que representa o for na linguagem
        return $this->elementsOfLanguage['loopCommands'][$indexOfElement];
    }

    /**
     * Retorna todos os dados do comando de desvio for da linguagem
     *
     * @return array
     */
    public function getBypassCommandWhile()
    {
        //adquire o índice do elemento while
        $indexOfElement = $this->indexOfLoopBypassCommand("while");
        //retorna o comando de desvio que representa o while na linguagem
        return $this->elementsOfLanguage['loopCommands'][$indexOfElement];
    }

    /**
     * Retorna a estrutura final do código fonte
     *
     * @return string
     */
    public function getEndCodeStructure()
    {
        return $this->elementsOfLanguage['endCodeStructure'];
    }
}