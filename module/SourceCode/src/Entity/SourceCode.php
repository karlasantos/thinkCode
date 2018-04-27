<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Entity;


use Application\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use User\Entity\User;

/**
 * Class SourceCode
 *  Representa os Códigos Fonte submetidos
 *
 * @ORM\Entity
 * @ORM\Table(name="source_codes")
 * @package SourceCode\Entity
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
     * Nome do arquivo do Código Fonte
     *
     * @ORM\Column(name="file_name", type="string", nullable=false)
     *
     * @var string
     */
    private $fileName;

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
     * Indica se esse código fonte é referencial ou não
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     * @var boolean
     */
    private $referential;

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
     * @ORM\ManyToOne(targetEntity="User\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @var User
     */
    private $user;

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
     * Define o nome de arquivo do Código Fonte
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Retorna o nome de arquivo do Código Fonte
     * @param string $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
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
     * Retorna se o Código Fonte é referencial ou não
     * @return bool
     */
    public function isReferential()
    {
        return $this->referential;
    }

    /**
     * Define se o Código Fonte é referencial ou não
     * @param bool $referential
     */
    public function setReferential($referential)
    {
        $this->referential = $referential;
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
     * Retorna todos os dados do Código Fonte em formato de array
     * @inheritdoc
     * @return array
     */
    public function toArray()
    {
        return array(
            'id'             => $this->id,
            'fileName'       => $this->fileName,
            'content'        => $this->content,
            'submissionDate' => $this->submissionDate->format('d-m-Y H:i:s'),
            'referential'    => $this->referential,
            'problem'        => $this->problem->toArray()
        );
    }
}