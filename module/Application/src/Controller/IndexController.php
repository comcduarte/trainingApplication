<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Adapter\AdapterAwareTrait;
use Training\Form\FindTrainingForm;
use Employee\Form\FindEmployeeForm;

class IndexController extends AbstractActionController
{
    use AdapterAwareTrait;
    
    public function indexAction()
    {
        $view = new ViewModel();
        
        $find_training_form = new FindTrainingForm();
        $find_training_form->setDbAdapter($this->adapter);
        $find_training_form->initialize();
        
        $find_employee_form = new FindEmployeeForm();
        $find_employee_form->setDbAdapter($this->adapter);
        $find_employee_form->initialize();
        
        $view->setVariables([
            'FindTrainingForm' => $find_training_form,
            'FindEmployeeForm' => $find_employee_form,
        ]);
        
        return $view;
    }
}
