<?php
namespace Training\Controller;

use Midnet\Controller\AbstractBaseController;
use Training\Model\TrainingModel;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Join;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Like;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Expression;

class TrainingController extends AbstractBaseController
{
    public $EmployeeClassesForm;
    
    public function updateAction()
    {
        $view = new ViewModel();
        $view = parent::updateAction();
        
        /****************************************
         * EMPLOYEE SUBTABLE
         ****************************************/
        $sql = new Sql($this->adapter);
        $select = new Select();
        $select->columns(['UUID'])
            ->from('employee_classes')
            ->join('employees', 'employee_classes.EMP_UUID = employees.UUID', ['UUID_E' => 'UUID', 'Employee ID' => 'EMP_NUM', 'Employee Name' => new Expression("CONCAT(LNAME, '\, ', FNAME)")], Join::JOIN_INNER)
            ->where([new Like('CLASS_UUID', $this->model->UUID)]);
            
        $statement = $sql->prepareStatementForSqlObject($select);
        
        $results = $statement->execute();
        $resultSet = new ResultSet($results);
        $resultSet->initialize($results);
        $roster = $resultSet->toArray();
        
        
        $view->setVariable('employee_classes_form', $this->EmployeeClassesForm);
        $view->setVariable('uuid', $this->model->UUID);
        $view->setVariable('roster', $roster);
        
        
        return $view;
    }
    
    public function assignAction()
    {
        $form = $this->EmployeeClassesForm;
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
                $model = new TrainingModel($this->adapter);
                $data = $form->getData();
                
                $class_uuid = $data['CLASS'];
                $employee_uuid = $data['EMP'];
                
                $model->read(['UUID' => $class_uuid]);
                $model->assignEmployee($employee_uuid);
                $model->update();
            }
        }
        
        //-- Return to previous screen --//
        $url = $this->getRequest()->getHeader('Referer')->getUri();
        return $this->redirect()->toUrl($url);
    }
    
    public function unassignAction()
    {
        $join_uuid = $this->params()->fromRoute('uuid',0);
        $this->model->unassignEmployee($join_uuid);
        $url = $this->getRequest()->getHeader('Referer')->getUri();
        return $this->redirect()->toUrl($url);
    }
}
