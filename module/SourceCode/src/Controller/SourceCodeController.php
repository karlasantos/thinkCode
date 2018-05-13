<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace SourceCode\Controller;


use Application\Controller\RestfulController;
use Zend\View\Model\ViewModel;

/**
 * Class SourceCodeController
 * Controller de código fonte, responsável pela submissão de um código fonte e listagem dos códigos submetidos
 * @package SourceCode\Controller
 */
class SourceCodeController extends RestfulController
{
    /**
     * Retorna a interface de submissão de código fonte
     * @return ViewModel
     */
    public function submissionAction()
    {
        $problemId  = $this->params()->fromQuery('problemId');
        return new ViewModel(array('problemId' => $problemId));
    }

}