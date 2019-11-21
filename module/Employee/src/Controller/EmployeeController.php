<?php
namespace Employee\Controller;

use Midnet\Controller\AbstractBaseController;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Join;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\Like;
use Zend\View\Model\ViewModel;

class EmployeeController extends AbstractBaseController
{
    public function updateAction()
    {
        $view = new ViewModel();
        $view = parent::updateAction();
        
        /****************************************
         * CLASSES SUBTABLE
         ****************************************/
        $sql = new Sql($this->adapter);
        $select = new Select();
        $select->columns(['UUID'])
        ->from('employee_classes')
        ->join('classes', 'employee_classes.CLASS_UUID = classes.UUID', ['UUID_C' => 'UUID', 'Code' => 'CODE', 'Name' => 'NAME', 'Date' => 'DATE_SCHEDULE'], Join::JOIN_INNER)
        ->where([new Like('EMP_UUID', $this->model->UUID)])
        ->order('DATE_SCHEDULE DESC');
        
        $statement = $sql->prepareStatementForSqlObject($select);
        
        $results = $statement->execute();
        $resultSet = new ResultSet($results);
        $resultSet->initialize($results);
        $classes = $resultSet->toArray();
        
        $view->setVariable('classes', $classes);
        return $view;
    }
}