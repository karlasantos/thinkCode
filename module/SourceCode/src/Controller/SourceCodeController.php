<?php
/**
 * Created by PhpStorm.
 * User: karla
 * Date: 11/05/18
 * Time: 00:22
 */

namespace SourceCode\Controller;


use Application\Controller\RestfulController;
use Zend\View\Model\ViewModel;

class SourceCodeController extends RestfulController
{
    public function submissionAction()
    {
        $problemId  = $this->params()->fromQuery('problemId');
        return new ViewModel(array('problemId' => $problemId));
    }

}