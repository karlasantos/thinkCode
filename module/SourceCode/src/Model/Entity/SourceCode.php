<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Model\Entity;


use Application\Model\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use User\Model\Entity\User;

/**
 * Class SourceCode
 *  Representa os Códigos Fonte submetidos
 *
 * @ORM\Entity Mapeamento Objeto Relacional
 * @ORM\Table(name="source_codes")
 * @package SourceCode\Model\Entity
 */
class SourceCode extends Entity
{
    /**
     * Id de identificação do Código Fonte
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * Conteúdo do arquivo do Código Fonte
     *
     * @ORM\Column(type="text", nullable=false)
     *
     * @var string
     */
    private $content;

    /**
     * Data da submissão do Código Fonte
     *
     * @ORM\Column(name="submission_date", type="datetime", nullable=false)
     *
     * @var DateTime
     */
    private $submissionDate;

    /**
     * O problema que este Código Fonte soluciona
     *
     * @ORM\ManyToOne(targetEntity="Problem", fetch="LAZY", inversedBy="sourceCodes")
     * @ORM\JoinColumn(name="problem_id", referencedColumnName="id")
     * @var Problem
     */
    private $problem;

    /**
     * A Linguagem que o Código Fonte foi escrito
     *
     * @ORM\ManyToOne(targetEntity="Language", fetch="LAZY")
     * @ORM\JoinColumn(name="language_id", referencedColumnName="id")
     * @var Language
     */
    private $language;

    /**
     * O usuário dono deste Código Fonte
     *
     * @ORM\ManyToOne(targetEntity="User\Model\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @var User
     */
    private $user;

    /**
     * Resultados da Análise do Código
     *
     * @ORM\OneToOne(targetEntity="AnalysisResults", inversedBy="sourceCode", fetch="LAZY")
     * @ORM\JoinColumn(name="analysis_results_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @var AnalysisResults
     */
    private $analysisResults;

    /**
     * A colocação desse código fonte
     *
     * @ORM\OneToOne(targetEntity="Rank", mappedBy="sourceCode", cascade={"persist", "remove"})
     * @var Rank
     */
    private $ranking;

    /**
     * SourceCode constructor
     */
    public function __construct()
    {
        $this->setSubmissionDate();
    }

    /**
     * Retorna o Id de identificação do Código Fonte
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Retorna o conteúdo do Código Fonte
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Define o conteúdo do Código Fonte
     * @param string $content
     */
    public function setContent($content)
    {
        //converte o código para caracteres minúsculos
        $content = strtolower($content);
        $this->content = $content;
    }

    /**
     * Retorna a data de submissão do Código Fonte
     * @return DateTime
     */
    public function getSubmissionDate()
    {
        return $this->submissionDate;
    }

    /**
     * Define a data de submissão do Código Fonte
     * @param DateTime|null $submissionDate
     */
    public function setSubmissionDate($submissionDate = null)
    {
        if ($submissionDate === null) {
            $submissionDate = new DateTime('now');
        }

        $this->submissionDate = $submissionDate;
    }

    /**
     * Retorna o problema que este Código Fonte soluciona
     * @return Problem
     */
    public function getProblem()
    {
        return $this->problem;
    }

    /**
     * Define o problema que este Código Fonte soluciona
     * @param Problem $problem
     */
    public function setProblem($problem)
    {
        $this->problem = $problem;
    }

    /**
     * Retorna a Linguagem que o Código Fonte foi escrito
     * @return Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Define a Linguagem que o Código Fonte foi escrito
     *
     * @param Language $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * Retorna o usuário dono do Código Fonte
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Define o usuário dono do Código Fonte
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Retorna o resultados da análise do código fonte
     *
     * @return AnalysisResults
     */
    public function getAnalysisResults()
    {
        return $this->analysisResults;
    }

    /**
     *  Define os resultados da análise do código fonte
     *
     * @param AnalysisResults $analysisResults
     */
    public function setAnalysisResults($analysisResults)
    {
        $this->analysisResults = $analysisResults;
    }

    /**
     * Retorna a colocação desse código fonte
     *
     * @return Rank
     */
    public function getRanking()
    {
        return $this->ranking;
    }

    /**
     * Define a colocação desse código fonte
     *
     * @param Rank $ranking
     */
    public function setRanking($ranking)
    {
        $this->ranking = $ranking;
    }

    /**
     * Retorna todos os dados do Código Fonte em formato de array
     * @inheritdoc
     * @return array
     */
    public function toArray()
    {
        return array(
            'id'             => $this->id,
            'content'        => $this->content,
            'submissionDate' => $this->submissionDate->format('d-m-Y H:i:s'),
            'problem'        => $this->problem->toArray()
        );
    }
}