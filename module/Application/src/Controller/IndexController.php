<?php
/**
 * TCC - Ciência da Computação, URI Santo Ângelo
 * Orientador: Denílson Rodrigues da Silva <deniro@san.uri.br>
 * @author Karla dos Santos Lencina <karla.krs@outlook.com>
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Class IndexController
 * Controlador da aplicação
 * @package Application\Controller
 */
class IndexController extends AbstractActionController
{
    /**
     * Retorna a página inicial da aplicação
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        return new ViewModel();
    }
}
